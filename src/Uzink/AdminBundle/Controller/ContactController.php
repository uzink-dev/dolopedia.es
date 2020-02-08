<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContactController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'contact';
        $this->entityDescription = 'Solicitud de información';
        $this->entityFields = array(
            'name' => 'Nombre',
            'email' => 'Email',
            'phone' => 'Teléfono',
            'message' => 'Mensaje',
        );
    }

}
