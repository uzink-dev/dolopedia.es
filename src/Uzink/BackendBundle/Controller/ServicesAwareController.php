<?php
namespace Uzink\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Uzink\BackendBundle\Handler\BreadcrumbHandler;

class ServicesAwareController extends Controller {
    /**
     * @var BreadcrumbHandler
     */
    protected $breadcrumbHandler;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->breadcrumbHandler = $this->get('uzink.breadcrumb.handler');
    }

    public function makeBreadcrumb($breadcrumb) {
        if (is_array($breadcrumb))
            $this->breadcrumbHandler->makeBreadcrumb($breadcrumb);
        else
            $this->breadcrumbHandler->makeBreadcrumbByEntity($breadcrumb);
    }
} 