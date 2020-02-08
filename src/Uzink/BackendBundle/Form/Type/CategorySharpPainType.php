<?php

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Form\ChoiceList\CategorySharpPainChoicelist;

class CategorySharpPainType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'choice_list'   => new CategorySharpPainChoicelist()
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'categorySharpPain';
    }
}