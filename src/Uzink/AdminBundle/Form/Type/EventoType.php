<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\CoreBundle\Form\Type\EventoType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EventoType
 * @package Uzink\AdminBundle\Form\Type
 */
class EventoType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('fecha', 'datetimepicker', array(
                'label' => 'Fecha',
            ))
        ;

    }
}
