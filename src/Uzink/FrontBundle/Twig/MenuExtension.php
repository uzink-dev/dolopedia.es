<?php

namespace Uzink\FrontBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Twig_Environment;

class MenuExtension extends \Twig_Extension
{
    protected $em;
    protected $templating;

    public function __construct(EntityManager $em, Twig_Environment  $templating) {
        $this->em = $em;
        $this->templating = $templating;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('renderMenu', array($this, 'renderMenuFilter'), array('is_safe' => array('all'))),
        );
    }

    public function renderMenuFilter($menu)
    {
        $repo = $this->em->getRepository('BackendBundle:Menu');

        $criteria = array(
            'title' => $menu
        );
        $menu = $repo->findOneBy($criteria);
        if (!$menu) return '';

        $items = $menu->getMenuItems();

        return $this->templating->render('FrontBundle:Cms:public.layout.menu.html.twig', array('items' => $items));
    }

    public function getName()
    {
        return 'menu_extension';
    }
}
