<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\CoreBundle\Form\Type\BlogPostType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BlogPostType
 * @package Uzink\AdminBundle\Form\Type
 */
class BlogPostType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }
}
