<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Menu
 *
 * @ORM\Table("menu")
 * @ORM\Entity
 * @UniqueEntity("title")
 */
class Menu
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu", cascade={"persist", "remove"})
     */
    private $menuItems;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuItems = new ArrayCollection();
    }

    /**
     * Get id.
    
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     *
     * @return Menu
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * Add menuItem.
     *
     * @param MenuItem $menuItem
     *
     * @return Menu
     */
    public function addMenuitem(MenuItem $menuItem)
    {
        $menuItem->setMenu($this);
        $this->menuItems->add($menuItem);
    
        return $this;
    }

    /**
     * Remove menuItem.
     *
     * @param MenuItem $menuItem
     */
    public function removeMenuitem(MenuItem $menuItem)
    {
        $this->menuItems->removeElement($menuItem);
    }

    /**
     * Get menuItems.
    
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }
}
