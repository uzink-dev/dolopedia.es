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
 * Class CategoryType
 * @package Uzink\AdminBundle\Form
 */
class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Título',
            ))
            ->add('owner', 'entity', array(
                'label' => 'Lider',
                'class' => 'BackendBundle:User',
                'group_by' => 'role',
                'empty_value' => 'Asigna un Lider',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                        $qb = $er->createQueryBuilder('u');
                        $qb->where(
                            $qb->expr()->orX(
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_SUPERVISOR . '%')),
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_ADMIN . '%')),
                                $qb->expr()->like('u.roles', $qb->expr()->literal('%' . User::ROLE_SUPER_ADMIN . '%'))
                            )
                        );
                        return $qb->orderBy('u.name', 'ASC');
                    },
            ))
            ->add('adHeaderZone', 'text', array(
                'label' => 'Zona de anuncios de cabecera'
            ))
            ->add('adSideZone', 'text', array(
                'label' => 'Zona de anuncios lateral'
            ))
            ->add('adFooterZone', 'text', array(
                'label' => 'Zona de anuncios de pie'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Category',
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
        return 'admin_category_type';
    }
}
