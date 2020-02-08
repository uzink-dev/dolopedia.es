<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Form\ChoiceList\TimezoneChoiceList;

class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attrClass = array('class' => 'form-control');

        $builder
            ->add('name',
                  null,
                  array(
                      'label' => 'user.name',
                      'attr' => $attrClass
                  ))
            ->add('role',
                  'choice',
                  array(
                      'label' => 'user.type',
                      'choices' => [
                          User::ROLE_USER => User::ROLE_USER,
                          User::ROLE_EDITOR => User::ROLE_EDITOR,
                          User::ROLE_SUPERVISOR => User::ROLE_SUPERVISOR,
                          User::ROLE_LEADER => User::ROLE_LEADER,
                          User::ROLE_ADMIN => User::ROLE_ADMIN,
                          User::ROLE_SUPER_ADMIN => User::ROLE_SUPER_ADMIN,
                      ],
                      'attr' => array(
                          'class' => 'form-control'
                      ),
                      'disabled' => true,

                  ))
            ->add('surname1',
                  null,
                  array(
                      'label' => 'user.surname1',
                      'attr' => $attrClass
                  ))
            ->add('surname2',
                  null,
                  array(
                      'label' => 'user.surname2',
                      'attr' => $attrClass
                  ))
            ->add('email',
                  null,
                  array(
                      'label' => 'user.email',
                      'attr' => $attrClass
                  ))
            ->add('sector',
                  'choice',
                  array(
                      'label' => 'user.sector',
                      'choices' => array(
                          'Sanitario' => 'Sanitario',
                          'Comercial' => 'Comercial',
                          'Investigacion' => 'Investigacion'
                      ),
                      'empty_value' => 'Seleccione un sector',
                      'attr' => array(
                          'class' => 'form-control extendedLauncher',
                          'data-extended-on' => 'Sanitario',
                          'data-extended-info' => 'extendedSector'
                      )
                  ))
            ->add('job',
                  null,
                  array(
                      'label' => 'user.job',
                      'attr' => $attrClass
                  ))
            ->add('workplace',
                  null,
                  array(
                      'label' => 'user.workplace',
                      'empty_value' => 'Otro centro de trabajo',
                      'empty_data' => null,
                      'attr' => array(
                          'class' => 'form-control extendedLauncher',
                          'data-extended-on' => '',
                          'data-extended-info' => 'extendedCentro'
                      )
                  ))                
            ->add('otherWorkplace',
                  null,
                  array(
                      'label' => 'user.otherWorkplace',
                      'attr' => $attrClass
                  ))
            ->add('country',
                  'country',
                  array(
                      'label' => 'user.country',
                      'attr' => $attrClass,
                      'preferred_choices' => array('ES')
                  ))
            ->add('timezone',
                  'choice',
                  array(
                      'label' => 'user.timezone',
                      'choice_list' => new TimezoneChoiceList(),
                      'attr' => $attrClass
                  ))
            ->add('plainPassword',
                  'repeated',
                  array(
                      'type' => 'password',
                      'first_options' => array(
                          'label' => 'user.password',
                          'attr' => $attrClass
                      ),
                      'second_options' => array(
                          'label' => 'user.repassword',
                          'attr' => $attrClass
                      )
                  ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\User',
            'translation_domain' => 'dolopedia',
            'validation_groups' => array('Default', 'Profile'),
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_usertype';
    }
}
