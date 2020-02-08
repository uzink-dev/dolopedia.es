<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PromoterController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'promoter';
        $this->entityDescription = 'Patrocinadores';
        $this->entityFields = array(
            'title' => 'Nombre'
        );
    }

}
