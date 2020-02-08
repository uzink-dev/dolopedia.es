<?php

namespace Uzink\BackendBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Doctrine\Common\Persistence\ObjectManager;
use Uzink\BackendBundle\Entity\Article;

class UserHandler
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
    
    public function makeBreadcrumb($items) {
        $breadcrumbs = $this->container->get("white_october_breadcrumbs");
        $translator = $this->container->get('translator');
        
        $breadcrumbs->addItem(
                $translator->trans('home', array(), 'dolopedia'), 
                $this->container->get('router')->generate('public.home')
            );
        
        foreach ($items as $item) {
            $routeString = $translator->trans($item[0], array(), 'dolopedia');
            if (array_key_exists (1 , $item)) {
                $route = $this->container->get('router')->generate($item[1]);
            } else {
                $route = null;
            }
            $breadcrumbs->addItem($routeString, $route);
        }
    }    
}