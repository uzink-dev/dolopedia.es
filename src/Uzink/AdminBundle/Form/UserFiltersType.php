<?php

namespace Uzink\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Uzink\BackendBundle\Entity\User;

/**
 * Class UserFiltersType
 * @package Uzink\AdminBundle\Form
 */
class UserFiltersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Nombre',
            ))
            ->add('surnames', 'text', array(
                'label' => 'Apellidos',
            ))
            ->add('email', 'text', array(
                'label' => 'Email',
            ))
            ->add('createdAtFrom', 'date', array(
                'label' => 'Fecha de alta (Desde)',
                'widget' => 'single_text'
            ))
            ->add('createdAtTo', 'date', array(
                'label' => 'Fecha de alta (Hasta)',
                'widget' => 'single_text'
            ))
            ->add(
                'role',
                'choice',
                array(
                    'label' => 'Rol',
                    'choices' => array(
                        User::ROLE_USER => 'Sin rol asignado',
                        User::ROLE_EDITOR => 'Editor',
                        User::ROLE_SUPERVISOR => 'Supervisor',
                        User::ROLE_LEADER => 'Lider',
                        User::ROLE_ADMIN => 'Administrador',
                        User::ROLE_SUPER_ADMIN => 'Super-Administrador'
                    ),
                    'empty_value' => 'Seleccione un rol de usuario'
                )
            )
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_user__filter_type';
    }
}
