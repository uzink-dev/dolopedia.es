<?php

namespace Uzink\FrontBundle\Twig;


use Uzink\BackendBundle\Entity\Article;

class ArticleExtension extends \Twig_Extension
{
    protected $em;
    private  $router;

    public function __construct($em, $router) {
        $this->em = $em;
        $this->router = $router;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('content', array($this, 'contentFilter')),
            new \Twig_SimpleFilter('hasContent', array($this, 'hasContentFilter')),
        );
    }

    public function contentFilter($content)
    {
        try {
            $document = new \DOMDocument();
            $document->loadHTML(utf8_decode($content));

            $elements = $document->getElementsByTagName('a');

            foreach($elements as $element) {
                $id = $element->attributes->getNamedItem('data-internallinks-id');
                $type = $element->attributes->getNamedItem('data-internallinks-type');

                if ($id && $type) {
                    $id = $id->value;
                    $type = $type->value;
                    $item = $this->getItem($id, $type);

                    if ($item) {
                        $namedRoute = ($type == 'category')?'public.category.show':'public.article.show';
                        $route = $this->router->generate($namedRoute, array('slug' => $item->getSeoSlug()));

                        $link= $document->createElement('a');
                        $link->setAttribute('href', $route);
                        $link->setAttribute('class', 'internal-link');
                        $link->setAttribute('target', '_self');
                        $link->nodeValue = $element->nodeValue;

                        $element->parentNode->replaceChild($link, $element);
                    }
                }
            }

            $html = $document->saveHTML($document);
            $layout = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);

            return $layout;
        } catch (\Exception $e) {
            // TODO :: Handling exception
        }

        return '';
    }

    public function hasContentFilter($item, $name, Article $article)
    {
        $hasContent = false;

        $content = $article->getContent();

        if ($item['type'] == 'ckeditor') {
            if ($content && array_key_exists($name, $content)) {
                if (!empty($content[$name])) return true;
            }
        }

        if (array_key_exists('fields', $item)) {
            foreach ($item['fields'] as $key => $field) {
                if ($this->hasContentFilter($field, $key, $article)) return true;
            }
        }

        return $hasContent;
    }    

    public function getName()
    {
        return 'article_extension';
    }

    protected function getItem($id, $type) {
        $articleRepo = $this->em->getRepository('BackendBundle:Article');
        $categoryRepo = $this->em->getRepository('BackendBundle:Category');

        $item = null;

        if ($type == 'article') {
            $item = $articleRepo->find($id);
        } elseif ($type == 'category') {
            $item = $categoryRepo->find($id);
        }

        return $item;
    }
}
