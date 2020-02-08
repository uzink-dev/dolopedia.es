<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CenterController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'center';
        $this->entityDescription = 'Centros de trabajo';
        $this->entityFields = array(
            'title' => 'Nombre'
        );
    }

}
