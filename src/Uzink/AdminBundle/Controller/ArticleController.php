<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Uzink\AdminBundle\Form\DeleteUserType;

class ArticleController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'article';
        $this->entityDescription = 'Artículos';
        $this->entityFields = array(
            'id'            => 'Id',
            'title'         => 'Título',
            'owner'         => 'Propietario',
            'supervisor'    => 'Supervisor',
            'editor'        => 'Editor',
            'drafts'        => 'Borradores',
            'createdAt'     => 'Fecha de creación',
            'publishedAt'   => 'Fecha de publicación'
        );
    }
}
