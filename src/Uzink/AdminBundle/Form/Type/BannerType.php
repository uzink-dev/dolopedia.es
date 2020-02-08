<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\CoreBundle\Form\Type\BannerType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BannerType
 * @package Uzink\AdminBundle\Form\Type
 */
class BannerType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }
}
