<?php

namespace Uzink\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Nombre'
            ))
            ->add('email', 'text', array(
                'label' => 'Email'
            ))
            ->add('phone', 'text', array(
                'label' => 'Teléfono',
                'required' => false
            ))
            ->add('message', 'textarea', array(
                'label' => 'Mensaje'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Contact',
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'contact';
    }
}