<?php

namespace Uzink\BackendBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Doctrine\Common\Persistence\ObjectManager;
use Uzink\BackendBundle\Entity\Article;

class ArticleHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $container;
    private $formBuilder;

    public function __construct(ContainerInterface $container, ObjectManager $om, $entityClass, FormFactory $formBuilder)
    {
        $this->container = $container;
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->formBuilder = $formBuilder;
        $this->repository = $this->om->getRepository($this->entityClass);
    }
    
    public function makeBreadcrumb(Article $article, &$breadcrumbs = null) {
        if (!$breadcrumbs) {
            $translator = $this->container->get('translator');
            $breadcrumbs = $this->container->get("white_october_breadcrumbs");
            $homeText = $translator->trans('breadcrumbs.home', array(), 'dolopedia');
            $breadcrumbs->addItem($homeText, $this->container->get('router')->generate('public.home'));
            $articlesText = $translator->trans('article.articles', array(), 'dolopedia');
            $breadcrumbs->addItem($articlesText, $this->container->get('router')->generate('public.category.list'));
        }
        
        if ($article->getCategory()) {
            $this->container->get('uzink.category.handler')->makeBreadcrumb($article->getCategory(), $breadcrumbs);
        }
        
        $breadcrumbs->addItem(
                $article->getTitle(), 
                $this->container->get('router')->generate(
                        'public.article.show',
                        array('slug' => $article->getSeoSlug())
                )
        );
    }

    public function makeContentStructure($article) {
        $type = $article->getType();
        $structure = array();

        if ($type) {
            $classType = 'Uzink\\BackendBundle\\Form\\'.ucfirst($type) . 'Type';
            $formType = new $classType();
            $form = $this->formBuilder->create($formType);

            $fields = $form->all();

            foreach($fields['block_content'] as $field) {
                $this->iterateForm($structure, $field);
            }

        }

        return $structure;
    }

    private function iterateForm(&$structure, $field) {
        $structure[$field->getName()] = array();
        $structure[$field->getName()]['title'] = $field->getConfig()->getOption('label');
        $structure[$field->getName()]['type'] = $field->getConfig()->getType()->getName();

        if($field->count() > 0) {
            $structure[$field->getName()]['fields'] = array();
            foreach($field as $item) {
                $this->iterateForm($structure[$field->getName()]['fields'], $item);
            }
        }
    }
}