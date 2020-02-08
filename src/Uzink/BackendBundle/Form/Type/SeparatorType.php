<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SeparatorType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'mapped' => false
            )); 
    }    

    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'separator';
    }
}
