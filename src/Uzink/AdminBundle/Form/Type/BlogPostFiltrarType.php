<?php

namespace Uzink\AdminBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BlogPostFiltrarType
 * @package Uzink\FrontBundle\Form\Type
 */
class BlogPostFiltrarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('actividad', 'entity', array(
                'class' => 'PaseIVEACoreBundle:Actividad',
                'label' => 'Actividad',
                'required' => false,
                'property' => 'titulo',
            ))
            ->add('usuario', 'entity', array(
                'class' => 'PaseIVEACoreBundle:Usuario',
                'query_builder' => function(EntityRepository $entityRepository) {
                    /** @var \Uzink\CoreBundle\Repository\UsuarioRepository $entityRepository */
                    return $entityRepository->queryBloggers();
                },
                'label' => 'Blogger',
                'required' => false,
                'property' => 'username',
            ))
        ;
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
            'csrf_protection'   => false,
        ));
    }
}
