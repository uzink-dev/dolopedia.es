<?php

namespace Uzink\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PageType
 * @package Uzink\AdminBundle\Form
 */
class PageType extends AbstractType
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
                        )
                    ),
                    'uiColor' => '#ffffff',
                ),
            ))
            ->add('seoSlug', 'text', array(
                'label' => 'Slug',
            ))
            ->add('seoH1', 'text', array(
                'label' => 'Título',
            ))
            ->add('seoDescription', 'textarea', array(
                'label' => 'Descripción',
            ))
            ->add('seoKeywords', 'text', array(
                'label' => 'Keywords',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Page'
        ));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_page_type';
    }
}
