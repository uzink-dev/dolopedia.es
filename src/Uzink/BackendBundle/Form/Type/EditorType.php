<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditorType extends AbstractTypeExtension
{
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setOptional(array('aditionalPlugins'));

        $resolver->setDefaults(array(
            'config' => array(
                'extraPlugins' => 'internallinks,bibliography,multimedia,externallink'
            )
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('aditionalPlugins', $options)) {
            $options['config']['extraPlugins'] = $options['aditionalPlugins'];
            die($options['config']['extraPlugins']);
        }

        parent::buildForm($builder, $options);
    }


    public function getExtendedType()
    {
        return 'ckeditor';
    }
}