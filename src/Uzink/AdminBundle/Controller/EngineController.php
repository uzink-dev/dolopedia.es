<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

class EngineController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'engine';
        $this->entityDescription = 'Motores de búsqueda';
        $this->entityFields = array(
            'name' => 'Nombre'
        );
    }

}
