<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\CoreBundle\Form\Type\TipologiaType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TipologiaType
 * @package Uzink\AdminBundle\Form\Type
 */
class TipologiaType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }
}
