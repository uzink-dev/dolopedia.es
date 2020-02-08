<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'contact_page.name'
            ))
            ->add('email', 'text', array(
                'label' => 'contact_page.email'
            ))
            ->add('phone', 'text', array(
                'label' => 'contact_page.phone',
                'required' => false
            ))
            ->add('message', 'textarea', array(
                'label' => 'contact_page.message'
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