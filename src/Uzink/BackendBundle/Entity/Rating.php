<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Rating
 *
 * @ORM\Table("rating")
 * @ORM\Entity
 */
class Rating
{
    /**
     * @var BaseUser
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @var Article
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="ratings")
     */
    private $article;    

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float")
     */
    private $rating;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rating
     *
     * @param float $rating
     *
     * @return Rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating()
    {
        return $this->rating;
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
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set article
     *
     * @param \stdClass $article
     *
     * @return Rating
     */
    public function setArticle($article)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \stdClass 
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set owner
     *
     * @param \Uzink\BackendBundle\Entity\User $owner
     *
     * @return Rating
     */
    public function setOwner(\Uzink\BackendBundle\Entity\User $owner)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Get owner
     *
     * @return \Uzink\BackendBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
