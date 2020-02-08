<?php

namespace Uzink\BackendBundle\Form;

use Uzink\BackendBundle\Form\UserAccountType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserRegistrationType extends UserAccountType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('conditions',
                  'checkbox',
                  array(
                     'mapped' => false,
                     'required' => true
                  ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\User',
            'translation_domain' => 'dolopedia',
            'validation_groups' => array('Default', 'Registration'),
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_usertype';
    }
}
