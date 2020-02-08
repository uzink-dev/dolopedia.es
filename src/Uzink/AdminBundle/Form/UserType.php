<?php

namespace Uzink\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Form\ChoiceList\InterestChoiceList;
use Uzink\BackendBundle\Form\ChoiceList\TimezoneChoiceList;

/**
 * Class UserType
 * @package Uzink\AdminBundle\Form
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent',
                'entity',
                array(
                    'label' => 'Usuario Padre',
                    'class' => 'BackendBundle:User',
                    'group_by' => 'role',
                    'empty_value' => 'No tiene usuario padre',
                    'required' => false,
                    'query_builder' => function(EntityRepository $er) {
                        $qb = $er->createQueryBuilder('u');
                        $qb->where(
                            $qb->expr()->orX(
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_EDITOR . '%')),
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_SUPERVISOR . '%')),
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_LEADER . '%')),
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_ADMIN . '%')),
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_SUPER_ADMIN . '%'))
                            )
                        );
                        return $qb->orderBy('u.name', 'ASC');
                    },
                ))
            ->add(
                'roles',
                'choice',
                array(
                    'label' => 'Roles',
                    'choices' => array(
                        User::ROLE_EDITOR => 'Editor',
                        User::ROLE_SUPERVISOR => 'Supervisor',
                        User::ROLE_LEADER => 'Lider',
                        User::ROLE_ADMIN => 'Administrador',
                        User::ROLE_SUPER_ADMIN => 'Super-Administrador'
                    ),
                    'multiple' => true,
                    'empty_value' => 'Usuario'
                ))
            ->add('name', 'text', array(
                'label' => 'Nombre',
            ))
            ->add('surname1', 'text', array(
                'label' => 'Apellido 1',
            ))
            ->add('surname2', 'text', array(
                'label' => 'Apellido 2',
                'required' => false
            ))
            ->add('address', 'text', array(
                'label' => 'Dirección',
            ))
            ->add('interests', 'choice', array(
                'label' => 'Intereses',
                'choice_list' => new InterestChoiceList(),
                'multiple' => true
            ))
            ->add('cv', 'textarea', array(
                'label' => 'CV',
            ))
            ->add('country', 'country', array(
                'label' => 'Pais',
                'preferred_choices' => array('ES')
            ))
            ->add('timezone',
                'choice',
                array(
                    'label' => 'Huso Horario',
                    'choice_list' => new TimezoneChoiceList(),
                ))
            ->add('imageResource',
                'imagepicker',
                array(
                    'label' => 'Imagen',
                    'required' => false,
                    'empty_data' => null,
                    'size' => array(
                        'width' => 146,
                        'height' => 146
                    )
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\User',
            'translation_domain' => 'dolopedia'
        ));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_user_type';
    }
}
