<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content',
                'textarea',
                array(
                    'label' => 'article.comment'
                ))
            ->add(
                '_target',
                'hidden',
                array(
                    'mapped' => false
                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Comment',
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'comment';
    }
}