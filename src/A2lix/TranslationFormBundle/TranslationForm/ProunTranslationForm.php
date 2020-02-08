<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Symfony\Component\Form\FormRegistry,
    Doctrine\Common\Persistence\ManagerRegistry,
    A2lix\TranslationFormBundle\TranslationForm\TranslationForm;

/**
 * @author David ALLIX
 */
class ProunTranslationForm  extends TranslationForm
{
    
    private $typeGuesser;
    private $managerRegistry;

    /**
     *
     * @param \Symfony\Component\Form\FormRegistry $formRegistry
     * @param \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry
     */
    public function __construct(FormRegistry $formRegistry, ManagerRegistry $managerRegistry)
    {
        $this->typeGuesser = $formRegistry->getTypeGuesser();
        $this->managerRegistry = $managerRegistry;
    }

    /**
     *
     * @return type
     */
    public function getManagerRegistry()
    {
        return $this->managerRegistry;
    }
    
    /**
     *
     * @param type $translationClass
     * @return type
     */
    protected function getTranslatableFields($translationClass)
    {
        $translationClass = \Doctrine\Common\Util\ClassUtils::getRealClass($translationClass);
        $manager = $this->getManagerRegistry()->getManagerForClass($translationClass);
        $metadataClass = $manager->getMetadataFactory()->getMetadataFor($translationClass);

        $fields = array();
        foreach ($metadataClass->fieldMappings as $fieldMapping) {
            if (!in_array($fieldMapping['fieldName'], array('id', 'locale'))) {
                $fields[] = $fieldMapping['fieldName'];
            }
        }
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenOptions($class, $options)
    {
                
        $childrenOptions = array();

        // Clean some options
        unset($options['inherit_data']);
        unset($options['translatable_class']);
        
        $fields = array_flip($this->getTranslatableFields($class));

        if(isset($options['list_fields']))
        {
            foreach($fields as $key => $field)
            {
                if(!isset($options['list_fields'][$key]))
                {
                    unset($fields[$key]);
                }
            }
        }
        
        if(!isset($options['is_seo']) || !$options['is_seo'])
        {
            unset($fields['seo_slug']);
            unset($fields['seo_h1']);
            unset($fields['seo_description']);
            unset($fields['seo_keywords']);
        }
        else
        {
            
            foreach($fields as $field => $key)
            {
                if($field != 'seo_slug' && $field != 'seo_h1' && $field != 'seo_description' && $field != 'seo_keywords')
                {
                    unset($fields[$field]);
                }
            }
            
        }
        
        $fields = array_flip($fields);
        
        $fields = array_unique(array_merge(array_keys($options['fields']), $fields));
        
        
        // Custom options by field
        foreach ($fields as $child) {
            
            $childOptions = (isset($options['fields'][$child]) ? $options['fields'][$child] : array()) + array('required' => $options['required']);
            
            $childOptions = $this->guessMissingChildOptions($this->typeGuesser, $class, $child, $childOptions);
            
            if ($childOptions['display']) {
                unset($childOptions['display']);
                // Custom options by locale
                if (isset($childOptions['locale_options'])) {
                    $localesChildOptions = $childOptions['locale_options'];
                    unset($childOptions['locale_options']);

                    foreach ($options['locales'] as $locale) {
                        $localeChildOptions = isset($localesChildOptions[$locale]) ? $localesChildOptions[$locale] : array();
                        if (!isset($localeChildOptions['display']) || $localeChildOptions['display']) {
                            $childrenOptions[$locale][$child] = $localeChildOptions + $childOptions;
                        }
                    }

                // General options for all locales
                } else {
                    foreach ($options['locales'] as $locale) {
                        $childrenOptions[$locale][$child] = $childOptions;
                    }
                }
            }
            unset($childOptions['display']);
        }
        
        //die(var_dump($childrenOptions));
        
        return $childrenOptions;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function guessMissingChildOptions($guesser, $class, $property, $options)
    {
                
        if (!isset($options['field_type']) && ($typeGuess = $guesser->guessType($class, $property))) {
            $options['field_type'] = $typeGuess->getType();
        }

        if (!isset($options['pattern']) && ($patternGuess = $guesser->guessPattern($class, $property))) {
            $options['pattern'] = $patternGuess->getValue();
        }

        if (!isset($options['max_length']) && ($maxLengthGuess = $guesser->guessMaxLength($class, $property))) {
            $options['max_length'] = $maxLengthGuess->getValue();
        }

        if (!isset($options['display'])) {
            $options['display'] = true;
        }
        
        return $options;
    }

}
