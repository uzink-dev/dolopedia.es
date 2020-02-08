<?php

namespace Uzink\ConfiguracionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParametroConfiguracionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder
                    ->create('tab_datos', 'form', 
                        array(
                            'attr' => array('class' => "tab_datos active", 'tab_class' => 'active'),
                            'label' => "Datos",
                            'virtual' => true)
                        )
                        ->add('nombre', 'text', array('label' => "Código"))
                    )
            ->add($builder
                    ->create('tab_datos2', 'form', 
                        array(
                            'attr' => array('class' => "tab_datos2"),
                            'label' => "Datos2",
                            'virtual' => true)
                        )
                        ->add('valor', 'text', array('label' => "Valor"))
                    )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\ConfiguracionBundle\Entity\ParametroConfiguracion'
        ));
    }

    public function getName()
    {
        return 'proun_configuracionbundle_parametroconfiguracion';
    }
}
