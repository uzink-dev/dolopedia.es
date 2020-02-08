<?php

namespace Uzink\BackendBundle\Handler;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Category;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbHandler
{
    const TRANSLATION_DOMAIN = 'dolopedia';
    const HOME_ROUTE = 'public.home';

    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbFactory;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var Router
     */
    protected $router;

    public function makeBreadcrumb($items) {
        $this->breadcrumbFactory->addItem(
                $this->translator->trans('home', array(), self::TRANSLATION_DOMAIN),
                $this->router->generate(self::HOME_ROUTE)
            );
        
        foreach ($items as $item) {
            $routeString = $this->translator->trans($item[0], array(), self::TRANSLATION_DOMAIN);
            if (array_key_exists (1 , $item)) {
                $route = $this->router->generate($item[1]);
            } else {
                $route = null;
            }
            $this->breadcrumbFactory->addItem($routeString, $route);
        }
    }

    public function makeBreadcrumbByEntity($entity) {

        $homeString = $this->translator->trans('breadcrumbs.home', array(), self::TRANSLATION_DOMAIN);
        $this->breadcrumbFactory->addItem($homeString, $this->router->generate(self::HOME_ROUTE));

        switch (true) {
            case $entity instanceof Article:
                $articlesString = $this->translator->trans('article.articles', array(), self::TRANSLATION_DOMAIN);
                $this->breadcrumbFactory->addItem($articlesString, $this->router->generate('public.category.list'));
                $this->makeBreadcrumbArticle($entity);
                break;

            case $entity instanceof Category:
                $articlesString = $this->translator->trans('article.articles', array(), self::TRANSLATION_DOMAIN);
                $this->breadcrumbFactory->addItem($articlesString, $this->router->generate('public.category.list'));
                $this->makeBreadcrumbCategory($entity);
                break;
        }
    }

    protected function makeBreadcrumbArticle(Article $entity) {
        if ($entity->getCategory()) $this->makeBreadcrumbCategory($entity->getCategory());

        $this->breadcrumbFactory->addItem(
            $entity->getTitle(),
            $this->router->generate(
                'public.article.show',
                array('slug' => $entity->getSeoSlug())
            )
        );
    }

    protected function makeBreadcrumbCategory(Category $entity) {
        if ($entity->getParent()) {
            if ($entity->getParent())
                $this->makeBreadcrumbCategory($entity->getParent());

            $this->breadcrumbFactory->addItem(
                $entity->getTitle(),
                $this->router->generate(
                    'public.category.show',
                    array('slug' => $entity->getSeoSlug())
                )
            );
        }
    }

    /**
     * @param Breadcrumbs $breadcrumbFactory
     */
    public function setBreadcrumbFactory(Breadcrumbs $breadcrumbFactory)
    {
        $this->breadcrumbFactory = $breadcrumbFactory;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}