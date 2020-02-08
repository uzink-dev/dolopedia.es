<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class AccordionBlockType extends AbstractType
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
        return 'block';
    }

    public function getName()
    {
        return 'accordionBlock';
    }
}
