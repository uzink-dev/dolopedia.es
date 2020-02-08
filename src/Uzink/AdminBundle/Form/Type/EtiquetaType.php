<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\CoreBundle\Form\Type\EtiquetaType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EtiquetaType
 * @package Uzink\AdminBundle\Form\Type
 */
class EtiquetaType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }
}
