<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * BibliographicEntry
 *
 * @ORM\Table("bibliographic_entry")
 * @ORM\Entity
 * @ExclusionPolicy("all");
 */
class BibliographicEntry
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
     * @ORM\Column(name="uid", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */    
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="publication", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */
    private $publication;

    /**
     * @var string
     *
     * @ORM\Column(name="pages", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */
    private $pages;

    /**
     * @var string
     *
     * @ORM\Column(name="volume", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */
    private $volume;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=500, nullable=true)
     * @Expose
     * @Groups({"detail"})
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     * @Expose
     * @Groups({"detail"})
     */    
    private $position;
    
    /**
     * @var \Datetime
     *
     * @ORM\Column(name="createdat", type="datetime")
     */    
    private $createdAt;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \Datetime('now');
        $this->position = 2147483647;
    }       
    
    public function __toString() {
        if ($this->title) return $this->title;
        else return '';
    }


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
     * Set title
     *
     * @param string $title
     *
     * @return BibliographicEntry
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return BibliographicEntry
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set publication
     *
     * @param string $publication
     *
     * @return BibliographicEntry
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;
    
        return $this;
    }

    /**
     * Get publication
     *
     * @return string 
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Set volume
     *
     * @param string $volume
     *
     * @return BibliographicEntry
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    
        return $this;
    }

    /**
     * Get volume
     *
     * @return string 
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * Set year
     *
     * @param string $year
     *
     * @return BibliographicEntry
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return BibliographicEntry
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    
        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BibliographicEntry
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BibliographicEntry
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return BibliographicEntry
     */
    public function setLink($link)
    {
        $this->link = $link;
    
        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set pages
     *
     * @param string $pages
     *
     * @return BibliographicEntry
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    
        return $this;
    }

    /**
     * Get pages
     *
     * @return string
     */
    public function getPages()
    {
        return $this->pages;
    }
}
