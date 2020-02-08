<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\CoreBundle\Form\Type\ActividadType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ActividadType
 * @package Uzink\AdminBundle\Form\Type
 */
class ActividadType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // TODO: Reactivar enlaces de Actividad
        $builder
            ->remove('linksActividadEnlace')
        ;

        $builder
            ->add('fechaDesde', 'datepicker', array(
                'label' => 'Fecha desde',
            ))
            ->add('fechaHasta', 'datepicker', array(
                'label' => 'Fecha hasta',
            ))
        ;
        
        $builder
            ->add('descripcion', 'ckeditor', array(
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
                    ),
                    'uiColor' => '#ffffff',
                ),
            ))
        ;

        $builder
            ->add('ventaja', 'ckeditor', array(
                'config' => array(
                    'toolbar' => array(
                        array(
                            'name' => 'basicstyles',
                            'items' => array('BulletedList', 'NumberedList'),
                        ),
                    ),
                    'uiColor' => '#ffffff',
                ),
            ))
            ->add('recordatorio', 'ckeditor', array(
                'config' => array(
                    'toolbar' => array(
                        array(
                            'name' => 'basicstyles',
                            'items' => array('BulletedList', 'NumberedList'),
                        ),
                    ),
                    'uiColor' => '#ffffff',
                ),
            ))
        ;
    }
}
