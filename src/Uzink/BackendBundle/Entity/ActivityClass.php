<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityClass
 *
 * @ORM\Table("activity_class")
 * @ORM\Entity
 */
class ActivityClass
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="classType", type="string", length=500)
     */
    private $classType;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set classType
     *
     * @param string $classType
     *
     * @return ActivityClass
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;
    
        return $this;
    }

    /**
     * Get classType
     *
     * @return string
     */
    public function getClassType()
    {
        return $this->classType;
    }
}

