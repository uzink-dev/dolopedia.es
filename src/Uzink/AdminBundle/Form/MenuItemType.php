<?php

namespace Uzink\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Entity\MenuItem;

/**
 * Class PageType
 * @package Uzink\AdminBundle\Form
 */
class MenuItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Nombre',
            ))
            ->add(
                'type',
                'choice',
                array(
                    'label'     => 'Tipo',
                    'attr' => array(
                        'data-show-launcher' => 'true'
                    ),
                    'choices'   => array(
                        MenuItem::TYPE_ITEM_STATIC => 'Página estática',
                        MenuItem::TYPE_ITEM_INNER_LINK =>  'Link Interno',
                        MenuItem::TYPE_ITEM_EXTERNAL_LINK => 'Link Externo'
                    ),
                    'empty_value' => 'Seleccione un tipo'
                ))
            ->add(
                'url',
                'text',
                array(
                    'label' => 'Enlace',
                    'attr' => array(
                        'data-show-on' => MenuItem::TYPE_ITEM_EXTERNAL_LINK,
                        'placeholder' => 'Url de enlace'
                    )
                ))
            ->add(
                'route',
                'choice',
                array(
                    'attr' => array(
                        'data-show-on' => MenuItem::TYPE_ITEM_INNER_LINK
                    ),
                    'label' => 'Zona',
                    'choices'   => array(
                        'workflow.category.tree'    => 'Árbol de categorías',
                        'public.category.list'      => 'Artículos',
                        'workflow.article.list'     => 'Artículos asignados',
                        'public.net.center.index'   => 'Centros',
                        'public.home'               => 'Home',
                        'panel.user.comment.index'  => 'Mis Comentarios',
                        'panel.user.contact.index'  => 'Mis Contactos',
                        'panel.user.account.show'   => 'Mi Cuenta',
                        'panel.user.profile.show'   => 'Mi Perfil',
                        'panel.collaboration.index' => 'Mis Colaboraciones',
                        'panel.user.favouriteArticles.index'    => 'Mis Favoritos',
                        'panel.message.list'        => 'Mis Mensajes',
                        'workflow.request.list'     => 'Mis Solicitudes',
                        'public.net.promoter.index' => 'Patrocinadores',
                        'public.net.index'          => 'Red',
                        'public.user.registration.options'      => 'Registro de usuario (Opciones)',
                        'public.user.registration.facebook'     => 'Registro facebook',
                        'public.user.registration.default'      => 'Registro normal',
                        'public.user.registration.twitter'      => 'Registro twitter',
                        'public.contact'            => 'Contacto'
                    ),
                    'empty_value' => 'Seleccione una zona de la web'
                ))
            ->add('page',
                'entity',
                array(
                    'label' => 'Página',
                    'class' => 'BackendBundle:Page',
                    'attr' => array(
                        'data-show-on' => MenuItem::TYPE_ITEM_STATIC
                    ),
                    'empty_value' => 'Seleccione una página'
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\MenuItem',
            'error_bubbling' => true
        ));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_menuitem_type';
    }
}
