<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\CoreBundle\Form\Type\EnlaceType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EnlaceType
 * @package Uzink\AdminBundle\Form\Type
 */
class EnlaceType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }
}
