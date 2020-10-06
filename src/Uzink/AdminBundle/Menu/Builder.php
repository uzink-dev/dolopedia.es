<?php

namespace Uzink\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Builder
 * @package Uzink\AdminBundle\Menu
 */
class Builder
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * Constructor
     *
     * @param FactoryInterface $factory
     * @param RequestStack $requestStack
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    /**
     * Check if current route is in the provided routes
     *
     * @param $array_routes
     * @return bool
     */
    public function isCurrentRoute(array $array_routes)
    {
        $request = $this->requestStack->getMasterRequest();

        foreach ($array_routes as $route) {
            switch ($request->get('_route')) {
                case $route:
                case $route . "_edit":
                case $route . "_new":
                    return true;
                    break;
                default:
                    // Do Noting
                    break;
            }
        }

        return false;
    }

    /**
     * Create main menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav top_nav');

        $menu
            ->addChild('Inicio', array('route' => 'admin.homepage'))
            ->setCurrent($this->isCurrentRoute(array('admin.homepage')));

        $team = $menu
            ->addChild('Equipo', array('route' => 'admin.user.index'));

        $workflow = $menu
            ->addChild('Workflow', array('route' => 'admin.article.index'))
            ->setChildrenAttribute('class', 'extra-width')
            ->setAttribute('dropdown', true)
            ->setCurrent($this->isCurrentRoute(array(
                'admin.article.index',
                'admin.article.edit',
                'admin.article.new',
                'admin.category.index',
                'admin.category.edit',
                'admin.category.new',
            )));

        $workflow
            ->addChild('Artículos', array('route' => 'admin.article.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.article.index',
                'admin.article.edit',
                'admin.article.new',
            )));

        $workflow
            ->addChild('Categorías', array('route' => 'admin.category.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.category.index',
                'admin.category.edit',
                'admin.category.new',
            )));

        $cms = $menu
            ->addChild('CMS', array('route' => 'admin.menu.index'))
            ->setChildrenAttribute('class', 'extra-width')
            ->setAttribute('dropdown', true)
            ->setCurrent($this->isCurrentRoute(array(
                'admin.menu.index',
                'admin.menu.edit',
                'admin.menu.new',
                'admin.page.index',
                'admin.page.edit',
                'admin.page.new',
            )));

        $cms
            ->addChild('Menús', array('route' => 'admin.menu.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.menu.index',
                'admin.menu.edit',
                'admin.menu.new',
            )));

        $cms
            ->addChild('Páginas', array('route' => 'admin.page.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.page.index',
                'admin.page.edit',
                'admin.page.new',
            )));

        $net = $menu
            ->addChild('CMS', array('route' => 'admin.center.index'))
            ->setChildrenAttribute('class', 'extra-width')
            ->setAttribute('dropdown', true)
            ->setCurrent($this->isCurrentRoute(array(
                'admin.center.index',
                'admin.center.edit',
                'admin.center.new',
                'admin.promoter.index',
                'admin.promoter.edit',
                'admin.promoter.new',
            )));

        $net
            ->addChild('Centros', array('route' => 'admin.center.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.center.index',
                'admin.center.edit',
                'admin.center.new',
            )));

        $net
            ->addChild('Patrocinadores', array('route' => 'admin.promoter.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.promoter.index',
                'admin.promoter.edit',
                'admin.promoter.new',
            )));

        $configuration = $menu
            ->addChild('Configuración', array('route' => 'admin.engine.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.engine.index',
                'admin.engine.edit',
                'admin.engine.new',
            )));

        $contact = $menu
            ->addChild('Contacto', array('route' => 'admin.contact.index'))
            ->setCurrent($this->isCurrentRoute(array(
                'admin.contact.index',
                'admin.contact.edit',
                'admin.contact.new',
            )));

        return $menu;
    }
}
