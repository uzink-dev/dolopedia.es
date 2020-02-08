<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ImagePickerType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setOptional(
                array('size', 'colors')
            )
            ->addAllowedTypes(array(
                'size' => 'array',
                'colors' => 'array'
            ))
            ->setDefaults(array(
                'size' => array(
                    'height' => 110,
                    'width' => 110
                ),
                'colors' => array(
                    'background' => '#f0f0f0',
                    'foreground' => '#ff5b4e'
                )
            ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['size'] = $options['size'];
        $view->vars['colors'] = $options['colors'];
    }
    
    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'imagepicker';
    }
}
