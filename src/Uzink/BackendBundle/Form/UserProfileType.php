<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Form\ChoiceList\InterestChoiceList;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attrClass = array('class' => 'form-control');
        
        $builder
            ->add('address',
                  null,
                  array(
                      'label' => 'user.address',
                      'attr' => $attrClass
                  ))                
            ->add('interests',
                  'choice',
                  array(
                      'label' => 'user.interests',
                      'attr' => $attrClass,
                      'choice_list' => new InterestChoiceList(),
                      'multiple' => true
                  ))
            ->add('cv',
                  null,
                  array(
                      'label' => 'user.cv',
                      'attr' => $attrClass
                  ))
            ->add('socialProfiles',
                  'collection',
                  array(
                      'type' => 'text',
                      'attr' => $attrClass
                  ))
            ->add('imageResource',
                'imagepicker',
                array(
                    'label' => 'user.image',
                    'required' => false,
                    'empty_data' => null,
                    'size' => array(
                        'height' => 146,
                        'width' => 146
                    )
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\User',
            'translation_domain' => 'dolopedia',
            'validation_groups' => array('Default', 'Profile')
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_usertype';
    }
}
