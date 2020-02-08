<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Uzink\AdminBundle\Form\DeleteUserType;

class CategoryController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'category';
        $this->entityDescription = 'Categoría';
        $this->entityFields = array(
            'id'    => 'Id',
            'title' => 'Título',
        );
    }
}
