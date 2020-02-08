<?php

namespace Uzink\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatepickerType extends AbstractType
{
    protected $format = 'dd/MM/yyyy';

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => $this->format,
            'required' => false,
            'attr' => array(
                'data-format' => $this->format
            )
        ));
    }

    public function getParent()
    {
        return 'date';
    }

    public function getName()
    {
        return 'datepicker';
    }
}