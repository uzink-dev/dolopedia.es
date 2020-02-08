<?php

namespace Uzink\AdminBundle\Form\Type;

use Uzink\AdminBundle\Form\Type\DatepickerType;

class DatetimepickerType extends DatepickerType
{
    protected $format = 'dd/MM/yyyy H:mm';

    public function getParent()
    {
        return 'date';
    }

    public function getName()
    {
        return 'datetimepicker';
    }
}