<?php

namespace Uzink\UtilsBundle\Utils;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * 
 * SEOsable trait.
 *
 * Should be used inside entity.
 */
trait SEOsable
{
    /**
     * @Assert\Valid(deep=true)
     */
    
    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="seo_slug", type="string", length=255, unique=true)
     */
    protected $seoSlug;

    /**
     *
     * @ORM\Column(name="seo_keywords", type="string", length=255, nullable=true)
     */
    protected $seoKeywords;
    
    
    /**
     *
     * @ORM\Column(name="seo_description", type="string", length=255, nullable=true)
     */
    protected $seoDescription;
    
    /**
     *
     * @ORM\Column(name="seo_h1", type="string", length=255, nullable=true)
     */
    protected $seoH1;
    
    /**
     *
     * @ORM\Column(name="seo_url", type="string", length=255, nullable=true)
     */
    protected $seoUrl;    
    
    /**
     * Set slug
     *
     * @param string $titulo
     * @return Parent object
     */
    public function setSeoSlug($slug)     {
        $this->seoSlug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSeoSlug()     {
        return $this->seoSlug;
    }
    
    /**
     * Set seo_description
     *
     * @param string $seo_description
     * @return Parent object
     */
    public function setSeoDescription($seo_description)     {
        $this->seoDescription = $seo_description;

        return $this;
    }

    /**
     * Get seo_description
     *
     * @return string 
     */
    public function getSeoDescription()     {
        return $this->seoDescription;
    }
    
    
    /**
     * Set seo_keywords
     *
     * @param string $seo_keywords
     * @return Parent object
     */
    public function setSeoKeywords($seo_keywords)     {
        $this->seoKeywords = $seo_keywords;

        return $this;
    }

    /**
     * Get seo_description
     *
     * @return string 
     */
    public function getSeoKeywords()     {
        return $this->seoKeywords;
    }
    
    
    /**
     * Set seo_keywords
     *
     * @param string $seo_keywords
     * @return Parent object
     */
    public function setSeoH1($seo_h1)     {
        $this->seoH1 = $seo_h1;

        return $this;
    }

    /**
     * Get seo_description
     *
     * @return string 
     */
    public function getSeoH1()     {
        return $this->seoH1;
    }

    /**
     * Set seo_keywords
     *
     * @param string $seo_url
     * @return Parent object
     */
    public function setSeoUrl($seo_url)     {
        $this->seoUrl = $seo_url;

        return $this;
    }

    /**
     * Get seo_url
     *
     * @return string 
     */
    public function getSeoUrl()     {
        return $this->seoUrl;
    }    
    
    /**
     * Get sluggable fields
     * 
     * @return string
     */
    public function getSluggableFields()
    {
        return array('title', 'name');
    }

}
