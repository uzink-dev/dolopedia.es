<?php

namespace Uzink\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class HelpPageType
 * @package Uzink\AdminBundle\Form
 */
class HelpPageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Título',
            ))
            ->add('content', 'ckeditor', array(
                'label' => 'Contenido',
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
                        ),
                        array(
                            'name' => 'custom',
                            'items' => array('Image')
                        )
                    ),
                    'uiColor' => '#ffffff',
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array(
                        'instance' => 'admin'
                    )
                ),

            ))
            ->add('position', 'number', array(
                'label' => 'Posición',
            ))
            ->add('role', 'hidden')
            ->add('seoSlug', 'text', array(
                'label' => 'Slug',
            ))
            ->add('seoH1', 'text', array(
                'label' => 'Título (SEO)',
            ))
            ->add('seoDescription', 'textarea', array(
                'label' => 'Descripción (SEO)',
            ))
            ->add('seoKeywords', 'text', array(
                'label' => 'Keywords (SEO)',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\HelpPage'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_help_page_type';
    }
}
