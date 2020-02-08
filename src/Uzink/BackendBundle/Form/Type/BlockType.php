<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class BlockType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array('icon'))
            ->addAllowedTypes(array(
                'icon' => 'string'
            ))
            ->setDefaults(array(
                'icon' => 'icon-article',
                'mapped' => false
            )); 
    }    
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['icon'] = $options['icon'];
    }      
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'block';
    }
}
