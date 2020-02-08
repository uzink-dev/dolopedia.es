<?php

namespace Uzink\BackendBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Doctrine\Common\Persistence\ObjectManager;
use Uzink\BackendBundle\Entity\Category;

class CategoryHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $container;

    public function __construct(ContainerInterface $container, ObjectManager $om, $entityClass)
    {
        $this->container = $container;
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
    }
    
    public function makeBreadcrumb(Category $category, &$breadcrumbs = null) {
        if (!$breadcrumbs) {
            $translator = $this->container->get('translator');
            $breadcrumbs = $this->container->get("white_october_breadcrumbs");
            $homeText = $translator->trans('breadcrumbs.home', array(), 'dolopedia');
            $breadcrumbs->addItem($homeText, $this->container->get('router')->generate('public.home'));
            $articlesText = $translator->trans('article.articles', array(), 'dolopedia');
            $breadcrumbs->addItem($articlesText, $this->container->get('router')->generate('public.category.list'));
        }
        
        if ($parent = $category->getParent()) {
            if ($parent->getParent()) {
                $this->makeBreadcrumb($category->getParent(), $breadcrumbs);
            }
        }
        
        $breadcrumbs->addItem(
                $category->getTitle(), 
                $this->container->get('router')->generate(
                        'public.category.show', 
                        array('slug' => $category->getSeoSlug())
                )
        );
    }    
}