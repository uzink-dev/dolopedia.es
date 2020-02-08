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
 * Class ArticleType
 * @package Uzink\AdminBundle\Form
 */
class ArticleType extends AbstractType
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
            ->add('supervisor', 'entity', array(
                'label' => 'Supervisor',
                'class' => 'BackendBundle:User',
                'group_by' => 'role',
                'empty_value' => 'Asigna un Lider',
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
            ->add('editor', 'entity', array(
                'label' => 'Editor',
                'class' => 'BackendBundle:User',
                'group_by' => 'role',
                'empty_value' => 'Asigna un Lider',
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
            ->add('category', 'entity', array(
                'label' => 'Categoria',
                'class' => 'BackendBundle:Category'
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
            ->add('whitoutAds', 'checkbox', array(
                'label' => 'No mostrar anuncios en este artículo'
            ))
            ->add('seoH1', 'text', array(
                'label' => 'SEO - Título',
            ))
            ->add('seoDescription', 'textarea', array(
                'label' => 'SEO - Descripción',
            ))
            ->add('seoKeywords', 'text', array(
                'label' => 'SEO - Keywords',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Article',
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
        return 'admin_article_type';
    }
}
