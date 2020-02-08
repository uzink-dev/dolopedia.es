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
class DeleteUserType extends AbstractType
{
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userToArticles',
                'entity',
                array(
                    'label' => 'Nuevo Usuario Asignado',
                    'class' => 'BackendBundle:User',
                    'group_by' => 'role',
                    'description' => 'Usuario al que se le asignarán los artículos del usuario que se va a eliminar',
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
                            )
                            ->andWhere(
                                $qb->expr()->neq('u', ':user')
                            )
                            ->setParameter('user', $this->user);
                            return $qb->orderBy('u.name', 'ASC');
                    },
                    'empty_value' => 'Seleccione un usuario',
                    'required' => false
                ))
            ->add('userToTeam',
                'entity',
                array(
                    'label' => 'Nuevo Usuario Padre',
                    'class' => 'BackendBundle:User',
                    'group_by' => 'role',
                    'description' => 'Usuario que se asignará al equipo del usuario que se va a eliminar',
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
                            )
                                ->andWhere(
                                    $qb->expr()->neq('u', ':user')
                                )
                                ->setParameter('user', $this->user);
                            return $qb->orderBy('u.name', 'ASC');
                    },
                    'empty_value' => 'Seleccione un usuario',
                    'required' => false
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'mapped' => false,
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
        return 'admin_delete_user_type';
    }
}
