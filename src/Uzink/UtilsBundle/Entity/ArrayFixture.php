<?php

namespace Uzink\UtilsBundle\Entity;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ArrayFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    
    protected   $order = 0;
    
    protected   $data = array();
    
    protected   $dataClassName = '';
    
    protected   $translationClassName = '';

    protected   $manager;
    
    private     $container;
    
    public function getOrder()
    {
        return $this->order;
    }
    
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->insertData();
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    
    protected function insertData()
    {   
        foreach($this->data as $id_object => $object_data)
        {
            $object = $this->processObject($object_data);
            $this->addReference($this->findReference($id_object, $object_data), $object);
            $this->manager->persist($object);
            $this->manager->flush(); 
        }
    }
    
    protected function findReference($id_object, $object_data)
    {
        if(!isset($object_data['ref']))
        {
            $reference = '_ref_'.strtolower($this->getClassname($this->dataClassName)).'_'.$id_object;
        }
        else 
        {
            $reference = $object_data['ref'];
        }
        return $reference;
    }
    
    protected function processObject($object_data)
    {
        $object = new $this->dataClassName();
        
        foreach($object_data as $key_field => $field_data)
        {
            
            switch($this->getTypeField($key_field, $field_data))
            {
                default:
                    $object = $this->processField($object, $key_field, $field_data);
                    break;
                case 'COLLECTION':
                    $object = $this->processCollection($object, $key_field, $field_data);
                    break;
                case 'OBJECT_REFERENCE':
                    $object = $this->processReference($object, $key_field, $field_data);
                    break;
                case 'TRANSLATIONS':
                    $object = $this->processTranslations($object, $key_field, $field_data);
                    break;
                case 'KEY_REFERENCE':
                    break;
            }
            
        }
        return $object;
    }
    
    
    protected function getTranslationClass()
    {
        if($this->translationClassName == '')
        {
            $this->translationClassName = $this->dataClassName.'Translation';
        }
        return new $this->translationClassName();
    }
    
    protected function processCollection($object, $key_field, $field_data)
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        $method = 'set'.ucfirst(ArrayFixture::camelize($key_field));
        foreach($field_data as $reference)
        {
            $collection->add($this->manager->merge($this->getReference($reference)));
        }
        $object->$method($collection);
        return $object;
    }
    
    protected function processReference($object, $key_field, $field_data)
    {
        $method = 'set'.ucfirst(ArrayFixture::camelize($key_field));
        $reference = $field_data;
        $object->$method($this->manager->merge($this->getReference($reference)));
        return $object;
    }
    
    protected function processTranslations($object, $key_field, $field_data)
    {
        $translation = $this->getTranslationClass();  
        $lang_code = substr($key_field, 13);
        $translation->setLocale($lang_code);
        foreach($field_data as $key_translation_field => $translation_value)
        {
            if($this->getTypeField($key_translation_field, $translation_value) == 'OBJECT_REFERENCE')
            {
                $method = 'set'.ucfirst(ArrayFixture::camelize($key_translation_field));
                $reference = $translation_value;
                $translation->$method($this->manager->merge($this->getReference($reference)));
            }
            else
            {
                $method = 'set'.ucfirst(ArrayFixture::camelize($key_translation_field));
                $translation->$method($translation_value);
            }
        }
        $object->addTranslation($translation);
        return $object;
    }
    
    protected function processField($object, $key_field, $field_data)
    {
        $method = 'set'.ucfirst(ArrayFixture::camelize($key_field));
        $object->$method($field_data);
        return $object;
    }
    
    protected function getTypeField($key_field, $field_data)
    {
        if(substr($key_field, 0, 13) == '_translation_')
        {
            return 'TRANSLATIONS';
        }
        elseif(is_array($field_data))
        {
            return 'COLLECTION';
        }
        elseif($key_field == 'ref')
        {
            return 'KEY_REFERENCE';
        }
        elseif(substr($field_data, 0, 5) == '_ref_')
        {
            return 'OBJECT_REFERENCE';
        }
        return 'FIELD';
    }
    
    protected function getClassname($classname)
    {
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) 
        {
            $classname = $matches[1];
        }

        return $classname;
    }
    
    protected static function camelize($id)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $id);
    }
    
}