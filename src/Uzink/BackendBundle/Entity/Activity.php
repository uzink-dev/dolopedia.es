<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Activity
 *
 * @ORM\Table("activity")
 * @ORM\Entity
 */
class Activity
{
    const EVENT_CREATE_ACTIVITIES = 'event.create.activities';

    const TYPE_ARTICLE_ADD_TO_FAVOURITE = 'activity.article.add_to_favourite';
    const TYPE_ARTICLE_REMOVE_TO_FAVOURITE = 'activity.article.remove_to_favourite';
    const TYPE_ARTICLE_PUBLISHED = 'activity.article.published';
    const TYPE_ARTICLE_NEW_SUPERVISOR = 'activity.article.new_supervisor';
    const TYPE_ARTICLE_NEW_EDITOR = 'activity.article.new_editor';
    const TYPE_ARTICLE_REMOVE = 'activity.article.remove';

    const TYPE_ARTICLE_REQUEST_NEW = 'activity.request.new';
    const TYPE_ARTICLE_REQUEST_MODIFY = 'activity.request.modify';
    const TYPE_ARTICLE_EDITION_CREATION = 'activity.request.edition.creation';
    const TYPE_ARTICLE_EDITION_MODIFICATION = 'activity.request.article.edition.modification';

    const TYPE_ARTICLE_EDITION_REVISION = 'activity.article.validation.revision';
    const TYPE_ARTICLE_EDITION_PUBLICATION = 'activity.article.validation.publication';
    const TYPE_ARTICLE_EDITION_VALIDATED = 'activity.article.validation.validated';
    const TYPE_ARTICLE_EDITION_NOT_VALIDATED = 'activity.article.validation.not_validated';
    const TYPE_ARTICLE_EDITION_PUBLISHED = 'activity.article.validation.published';
    const TYPE_ARTICLE_EDITION_NOT_PUBLISHED = 'activity.article.validation.not_published';

    const TYPE_REQUEST_ACCEPT = 'activity.request.accept';
    const TYPE_REQUEST_DECLINE = 'activity.request.decline';

    const TYPE_USER_FOLLOW = 'activity.user.follow';
    const TYPE_USER_UNFOLLOW = 'activity.user.unfollow';
    const TYPE_USER_NEW_MESSAGE = 'activity.user.new_message';
    const TYPE_USER_NEW_MULTIPLE_MESSAGE = 'activity.user.new_message';
    const TYPE_USER_NEW_TEAM_USER = 'activity.user.new_team_user';
    const TYPE_USER_DELETE_USER = 'activity.user.delete_user';
    const TYPE_USER_REGISTERED = 'activity.user.registered';
    const TYPE_USER_NEW_ADMIN = 'activity.user.new_admin';
    const TYPE_USER_NEW_LEADER = 'activity.user.new_leader';
    const TYPE_USER_NEW_SUPERVISOR = 'activity.user.new_supervisor';
    const TYPE_USER_NEW_EDITOR = 'activity.user.new_supervisor';

    const TYPE_CATEGORY_NEW = 'activity.category.new';
    const TYPE_CATEGORY_DELETE = 'activity.category.delete';
    const TYPE_CATEGORY_NEW_OWNER = 'activity.category.new_owner';


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Uzink\BackendBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Uzink\BackendBundle\Entity\User")
     */
    private $sender;

    /**
     * @var \Uzink\BackendBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="activities")
     */
    private $receiver;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=100)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="entityId", type="integer")
     */
    private $entityId;

    /**
     * @var ActivityClass
     *
     * @ORM\ManyToOne(targetEntity="ActivityClass")
     */
    private $entityClass;

    /**
     * @var array
     *
     * @ORM\Column(name="entity_metadata", type="array", nullable=true)
     */
    private $entityMetadata;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    function __construct()
    {
        $this->entityMetadata = array();
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
     * Set type.
     *
     * @param string $type
     *
     * @return Activity
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * Set entityMetadata.
    
     *
     * @param array $entityMetadata
     *
     * @return Activity
     */
    public function setEntityMetadata($entityMetadata)
    {
        $this->entityMetadata = $entityMetadata;
    
        return $this;
    }

    /**
     * Get entityMetadata.
    
     *
     * @return array
     */
    public function getEntityMetadata()
    {
        if (!$this->entityMetadata) return array();
        return $this->entityMetadata;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     *
     * @return Activity
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    
        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Activity
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Set entityClass
     *
     * @param \Uzink\BackendBundle\Entity\ActivityClass $entityClass
     *
     * @return Activity
     */
    public function setEntityClass(ActivityClass $entityClass = null)
    {
        $this->entityClass = $entityClass;
    
        return $this;
    }

    /**
     * Get entityClass
     *
     * @return \Uzink\BackendBundle\Entity\ActivityClass
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Activity
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set sender
     *
     * @param \Uzink\BackendBundle\Entity\User $sender
     *
     * @return Activity
     */
    public function setSender(User $sender = null)
    {
        $this->sender = $sender;
    
        return $this;
    }

    /**
     * Get sender
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set receiver
     *
     * @param \Uzink\BackendBundle\Entity\User $receiver
     *
     * @return Activity
     */
    public function setReceiver(User $receiver = null)
    {
        $this->receiver = $receiver;
    
        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
