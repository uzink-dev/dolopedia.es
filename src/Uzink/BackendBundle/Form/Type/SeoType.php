<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class SeoType extends AbstractType
{
    public function getParent()
    {
        return 'block';
    }

    public function getName()
    {
        return 'seo';
    }
}
