<?php

namespace Uzink\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EngineType
 * @package Uzink\AdminBundle\Form
 */
class MenuType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Nombre',
                'description' => 'Este será el nombre por el que se referencie al menú desde la web, modificar este valor puede provocar efectos no deseados'
            ))
            ->add('menuItems', 'collection', array(
                'label'     => 'Páginas',
                'type'      => new MenuItemType(),
                'allow_add' => true,
                'allow_delete' => true,
                'cascade_validation' => true,
                'error_bubbling' => true,
                'by_reference' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Menu',
            'cascade_validation' => true,
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
        return 'admin_menu_type';
    }
}
