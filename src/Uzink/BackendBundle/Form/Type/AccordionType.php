<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class AccordionType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array('separator'))
            ->setDefaults(array(
                'mapped' => false
            )); 
    }    

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        if (array_key_exists('separator', $options)) 
            $view->vars['separator'] = $options['separator'];
    }     
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'accordion';
    }
}
