<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Uzink\UtilsBundle\Utils\SEOsable;

/**
 * Page
 *
 * @ORM\Table("menu_item")
 * @ORM\Entity
 */
class MenuItem
{
    const TYPE_ITEM_STATIC = 'page.static';
    const TYPE_ITEM_INNER_LINK = 'page.inner.link';
    const TYPE_ITEM_EXTERNAL_LINK = 'page.external.link';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="menuItems")
     */
    private $menu;

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Page")
     */
    private $page;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", nullable=true)
     */
    private $route;

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
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Page
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get menu.
     *
     * @return \Uzink\BackendBundle\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set menu.
     *
     * @param \Uzink\BackendBundle\Entity\Menu $menu
     *
     * @return Page
     */
    public function setMenu(Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Set page
     *
     * @param \Uzink\BackendBundle\Entity\Page $page
     *
     * @return MenuItem
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
    
        return $this;
    }

    /**
     * Get page
     *
     * @return \Uzink\BackendBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set route
     *
     * @param string $route
     *
     * @return MenuItem
     */
    public function setRoute($route)
    {
        $this->route = $route;
    
        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }
}
