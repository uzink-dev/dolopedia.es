<?php

namespace Uzink\AdminBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'page';
        $this->entityDescription = 'Páginas';
        $this->entityFields = array(
            'title' => 'Nombre'
        );
    }
}
