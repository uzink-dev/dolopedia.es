<?php

namespace Uzink\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PromoterType
 * @package Uzink\AdminBundle\Form
 */
class PromoterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Nombre',
            ))
            ->add('link', 'url', array(
                'label' => 'Url'
            ))
            ->add('description', 'ckeditor', array(
                'label' => 'Descripción',
                'config' => array(
                    'toolbar' => array(
                        array(
                            'name' => 'basicstyles',
                            'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript',
                                '-', 'RemoveFormat'),
                        ),
                        array(
                            'name' => 'paragraph',
                            'items' => array('BulletedList', 'NumberedList', '-', 'Outdent', 'Indent', '-', 'Blockquote',
                                '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                        ),
                        array(
                            'name' => 'links',
                            'items' => array('Link', 'Unlink'),
                        ),
                        array(
                            'name' => 'styles',
                            'items' => array('Format', 'Font', 'FontSize'),
                        )
                    ),
                    'uiColor' => '#ffffff',
                ),
            ))
            ->add('imageResource',
                'imagepicker',
                array(
                    'label' => 'Imagen',
                    'required' => false,
                    'empty_data' => null,
                    'size' => array(
                        'width' => 380,
                        'height' => 240
                    )
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Promoter'
        ));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_promoter_type';
    }
}
