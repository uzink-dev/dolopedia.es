<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class BibliographyType extends AbstractType
{
    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'bibliography';
    }
}
