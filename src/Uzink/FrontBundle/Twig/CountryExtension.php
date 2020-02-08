<?php
namespace Uzink\FrontBundle\Twig;

use Symfony\Component\Intl\Intl;
use Uzink\BackendBundle\Form\ChoiceList\TimezoneChoiceList;

class CountryExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('countryName', array($this, 'countryName')),
            new \Twig_SimpleFilter('timezoneName', array($this, 'timezoneName')),
        );
    }

    public function countryName($countryCode){
        return Intl::getRegionBundle()->getCountryName($countryCode);
    }

    public function timezoneName($timezoneCode){
        $timezoneList = new TimezoneChoiceList();

        $views = $timezoneList->getPreferredViews();
        foreach ($views as $view) {
            if ($view->data == $timezoneCode) return $view->label;
        }

        $views = $timezoneList->getRemainingViews();
        foreach ($views as $view) {
            if ($view->data == $timezoneCode) return $view->label;
        }

        return $timezoneCode;
    }

    public function getName()
    {
        return 'country_extension';
    }
}
?>