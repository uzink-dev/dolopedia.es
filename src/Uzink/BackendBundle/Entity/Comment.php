<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Comment
 *
 * @ORM\Table("comment")
 * @ORM\Entity
 */
class Comment
{
    const SCOPE_PUBLIC = 'public';
    const SCOPE_DISCUSSION = 'discussion';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     */
    private $owner;
    
    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments")
     */
    private $article;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="scope", type="string")
     */
    private $scope = self::SCOPE_PUBLIC;

    public function __construct() {
        $this->createdAt = new \DateTime('now');
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
     * Set article
     *
     * @param \stdClass $article
     *
     * @return Comment
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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comment
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
     * Set owner
     *
     * @param \Uzink\BackendBundle\Entity\User $owner
     *
     * @return Comment
     */
    public function setOwner(User $owner = null)
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

    /**
     * Set scope.
    
     *
     * @param string $scope
     *
     * @return Comment
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    
        return $this;
    }

    /**
     * Get scope.
    
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
}
