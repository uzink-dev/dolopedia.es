<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Uzink\UtilsBundle\Utils\SEOsable;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Center
 *
 * @ORM\Entity
 * @ORM\Table(name="center")
 * @Vich\Uploadable
 */
class Center
{
    use SEOsable;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    protected $updatedAt;
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="workplace")
     */
    private $users;
    /**
     * @ORM\Column(type="string", length=255, name="image", nullable=true)
     *
     * @var string $image
     */
    private $image;
    /**
     * @Vich\UploadableField(mapping="institution_image", fileNameProperty="image")
     *
     * @var File $imageResource
     */
    private $imageResource;
    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
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
     * Set title
     *
     * @param string $title
     *
     * @return Institution
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return Institution
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Institution
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Add users
     *
     * @param \Uzink\BackendBundle\Entity\User $users
     *
     * @return Center
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Uzink\BackendBundle\Entity\User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Institution
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return File
     */
    public function getImageResource()
    {

        return $this->imageResource;
    }

    public function setImageResource(File $imageResource)
    {
        $this->imageResource = $imageResource;
        if ($imageResource) {
            $this->updatedAt = new \DateTime('now');
        }
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

    private function getResource($resource)
    {
        if ($resource != null) {
            rewind($resource);
            return stream_get_contents($resource);
        } else {
            return null;
        }
    }
}
