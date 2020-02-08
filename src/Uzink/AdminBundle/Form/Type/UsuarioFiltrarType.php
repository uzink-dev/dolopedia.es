<?php

namespace Uzink\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UsuarioFiltrarType
 * @package Uzink\FrontBundle\Form\Type
 */
class UsuarioFiltrarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'Email',
                'required' => false,
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('nombre', null, array(
                'required' => false,
                'label' => 'profile.show.nombre',
                'translation_domain' => 'FOSUserBundle',
            ))
            ->add('dni', null, array(
                'required' => false,
                'label' => 'profile.show.dni',
                'translation_domain' => 'FOSUserBundle',
            ))
        ;

        //TODO: Añadir limitación a que un campo del form este relleno
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        return $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }
}
