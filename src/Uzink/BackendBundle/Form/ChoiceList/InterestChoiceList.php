<?php

namespace Uzink\BackendBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class InterestChoiceList extends LazyChoiceList {
    protected function loadChoiceList()
    {
        $choices = array(
            'Anestesia',
            'Tratamiento del dolor',
            'Causas del dolor',
            'Medicina',
            'Cirugía',
            'Tratamientos generales',
            'Tratamientos paliativos'
        );
        
        $labels = array(
            'Anestesia',
            'Tratamiento del dolor',
            'Causas del dolor',
            'Medicina',
            'Cirugía',
            'Tratamientos generales',
            'Tratamientos paliativos'
        );
        
        return new ChoiceList($choices, $labels);
    }
}

?>
