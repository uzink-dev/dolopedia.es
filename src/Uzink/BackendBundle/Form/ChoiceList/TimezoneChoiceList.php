<?php

namespace Uzink\BackendBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class TimezoneChoiceList extends LazyChoiceList {
    protected function loadChoiceList()
    {
        $choices = array(
            'GMT-11',
            'GMT-10',
            'GMT-09',
            'GMT-08',
            'GMT-07',
            'GMT-06',
            'GMT-05',
            'GMT-04',
            'GMT-03',
            'GMT-02',
            'GMT-01',
            'GMT+00',
            'GMT+01',
            'GMT+02',
            'GMT+03',
            'GMT+04',
            'GMT+05',
            'GMT+06',
            'GMT+07',
            'GMT+08',
            'GMT+09',
            'GMT+10',
            'GMT+11',
            'GMT+12'
        );
        
        $labels = array(
            '(GMT-11) Samoa',
            '(GMT-10) Hawaii',
            '(GMT-09) Anchorage, Juneau',
            '(GMT-08) Seattle, San Francisco, Los Angeles',
            '(GMT-07) Edmonton, Denver, Phoenix',
            '(GMT-06) Winnipeg, Chicago, Houston, Mexico, Tegucigalpa, Managua, San Jose',
            '(GMT-05) New York, Miami, La Habana, Puerto Principe, Panama, Bogota, Quito, Lima',
            '(GMT-04) Halifax, Santo Domingo, Caracas, Georgetown, Manaus, La Paz, Asuncion, Santiago de Chile',
            '(GMT-03) Brasilia, Rio De Janeiro, Montevideo, Buenos Aires',
            '(GMT-02) Recife',
            '(GMT-01) Azores',
            '(GMT+00) Londres, Dublín, Lisboa, Casablanca, Dakar, Accra',
            '(GMT+01) Paris, Madrid, Roma, Berlín, Praga, Belgrado, Varsovia, Estocolmo, Oslo, Argel, Lagos, Brazzaville, Luanda',
            '(GMT+02) Helsinki, Minks, Bucarest, Estambul, Atenas, Beirut, Cairo, Tripoli, Harare, Ciudad del Cabo',
            '(GMT+03) San Petersburgo, Moscow, Bagdad, Riad, Addis Abeba, Kampala, Nairobi, Mogadisco',
            '(GMT+04) Samara, Baku, Tbilisi, Dubai',
            '(GMT+05) Sheliabinsk, Karachi, Islamabad',
            '(GMT+06) Omsk, Tashkent, Dacca',
            '(GMT+07) Novosibirsk, Bangkok, Hanoi, Yakarta',
            '(GMT+08) Irkutsk, Lhasa, Beijing, Hong Kong, Kuala Lumpur, Singapur, Manila, Perth',
            '(GMT+09) Tokyo, Seul',
            '(GMT+10) Vladivostok, Sydney, Melbourne',
            '(GMT+11) Noumea, Magaban',
            '(GMT+12) Wellington (Nueva Zelanda)'
        );

         $preferredChoices = array(
             'GMT+01'
         );
        
        return new ChoiceList($choices, $labels, $preferredChoices);
    }
}

?>
