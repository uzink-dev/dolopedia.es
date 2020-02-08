<?php

namespace Uzink\UtilsBundle\Utils;

/**
 * 
 * SEOsable trait.
 *
 * Should be used inside entity.
 */
trait SEOsableMethods
{
    
    public function getSeoFields()
    {
        return $this->getTranslations();
    }
    
    public function setSeoFields($seoFields)
    {
        foreach($this->getTranslations() as $culture => $translation)
        {
            $translation->setSeoH1($seoFields[$culture]->getSeoH1());
            $translation->setSeoDescription($seoFields[$culture]->getSeoDescription());
            $translation->setSeoSlug($seoFields[$culture]->getSeoSlug());
            $translation->setSeoKeywords($seoFields[$culture]->getSeoKeywords());
        }
        return $this->getTranslations();
    }
    

    
}
