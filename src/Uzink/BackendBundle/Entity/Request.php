<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Request
 *
 * @ORM\Table("request")
 * @ORM\Entity(repositoryClass="Uzink\BackendBundle\Entity\RequestRepository")
 * @Vich\Uploadable
 */
class Request
{
    use ORMBehaviors\Timestampable\Timestampable;

    // Request Process
    const STATUS_REQUEST_CREATED = 'request_created';
    const STATUS_REQUEST_ACCEPTED = 'request_accepted';
    const STATUS_REQUEST_DECLINED = 'request_declined';

    // Edition Process
    const STATUS_EDITION_REQUESTED = 'edition_requested';
    const STATUS_EDITION_CREATED = 'edition_created';
    const STATUS_EDITION_MODIFIED = 'edition_modified';

    const STEP_CREATE = 'create';
    const STEP_ACCEPT = 'accept';
    const STEP_DECLINE = 'decline';
    const STEP_REQUEST = 'request';
    const STEP_MODIFY = 'modify';

    // These request types are used in the workflow process
    // Article Request Process
    const TYPE_REQUEST_NEW = 'request_new';
    const TYPE_REQUEST_MODIFY = 'request_modify';

    // Article Edition Process
    const TYPE_EDITION_CREATION = 'edition_creation';
    const TYPE_EDITION_MODIFICATION = 'edition_modification';

    const TYPE_ARTICLE_VALIDATION = 'article_validation';
    const TYPE_ARTICLE_PUBLICATION = 'article_publication';

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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type = self::TYPE_REQUEST_NEW;

    /**
     * @var \Uzink\BackendBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sentRequests")
     */
    private $userFrom;

    /**
     * @var \Uzink\BackendBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requests")
     */
    private $userTo;

    /**
     * @var \Uzink\BackendBundle\Entity\Article
     *
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="request")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $article;

    /**
     * @var \Uzink\BackendBundle\Entity\Draft
     *
     * @ORM\ManyToOne(targetEntity="Draft")
     * @ORM\JoinColumn(name="draft_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $draft;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \Uzink\BackendBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $category;

    /**
     *
     * @Vich\UploadableField(mapping="attachments_file", fileNameProperty="attachmentName")
     *
     * @var File
     */
    private $attachmentFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $attachmentName;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var \Uzink\BackendBundle\Entity\Request
     *
     * @ORM\OneToOne(targetEntity="Request")
     * @ORM\JoinColumn(name="previousRequest_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $previousRequest;

    /**
     * @var \Uzink\BackendBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $assignedUser;

    /**
     * @var text
     *
     * @ORM\Column(name="reason_to_decline", type="text", nullable=true)
     */
    private $reasonToDecline;

    /**
     * @return string
     */
    public function __toString() {
        return $this->title;
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
     * Set userFrom
     *
     * @param \Uzink\BackendBundle\Entity\User $userFrom
     *
     * @return Request
     */
    public function setUserFrom(User $userFrom)
    {
        $this->userFrom = $userFrom;
    
        return $this;
    }

    /**
     * Get userFrom
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * Set userTo
     *
     * @param \Uzink\BackendBundle\Entity\User $userTo
     *
     * @return Request
     */
    public function setUserTo(User $userTo)
    {
        $this->userTo = $userTo;
    
        return $this;
    }

    /**
     * Get userTo
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getUserTo()
    {
        return $this->userTo;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Request
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
     * Set content
     *
     * @param string $content
     *
     * @return Request
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
     * Set category
     *
     * @param \Uzink\BackendBundle\Entity\Category $category
     *
     * @return Request
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Uzink\BackendBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $attachment
     */
    public function setAttachmentFile(File $attachment = null)
    {
        $this->attachmentFile = $attachment;

        if ($attachment) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getAttachmentFile()
    {
        return $this->attachmentFile;
    }

    /**
     * @param string $attachmentName
     */
    public function setAttachmentName($attachmentName)
    {
        $this->attachmentName = $attachmentName;
    }

    /**
     * @return string
     */
    public function getAttachmentName()
    {
        return $this->attachmentName;
    }

    /**
     * Set article
     *
     * @param \Uzink\BackendBundle\Entity\Article $article
     *
     * @return Request
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \Uzink\BackendBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Request
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function isFinished()
    {
        switch ($this->type) {
            case self::TYPE_REQUEST_NEW:
            case self::TYPE_REQUEST_MODIFY:
            case self::TYPE_ARTICLE_VALIDATION:
            case self::TYPE_ARTICLE_PUBLICATION:
                if ($this->status == self::STATUS_REQUEST_CREATED) return false;
                break;

            case self::TYPE_EDITION_CREATION:
            case self::TYPE_EDITION_MODIFICATION:
                if ($this->status == self::STATUS_EDITION_REQUESTED) return false;
                break;
        }

        return true;
    }

    /**
     * Set reasonToDecline
     *
     * @param string $reasonToDecline
     *
     * @return Request
     */
    public function setReasonToDecline($reasonToDecline)
    {
        $this->reasonToDecline = $reasonToDecline;
    
        return $this;
    }

    /**
     * Get reasonToDecline
     *
     * @return string
     */
    public function getReasonToDecline()
    {
        return $this->reasonToDecline;
    }

    /**
     * Set assignedUser
     *
     * @param \Uzink\BackendBundle\Entity\User $assignedUser
     *
     * @return Request
     */
    public function setAssignedUser(\Uzink\BackendBundle\Entity\User $assignedUser = null)
    {
        $this->assignedUser = $assignedUser;
    
        return $this;
    }

    /**
     * Get assignedUser
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getAssignedUser()
    {
        return $this->assignedUser;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Request
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set previousRequest
     *
     * @param \Uzink\BackendBundle\Entity\Request $previousRequest
     *
     * @return Request
     */
    public function setPreviousRequest(\Uzink\BackendBundle\Entity\Request $previousRequest = null)
    {
        $this->previousRequest = $previousRequest;
    
        return $this;
    }

    /**
     * Get previousRequest
     *
     * @return \Uzink\BackendBundle\Entity\Request
     */
    public function getPreviousRequest()
    {
        return $this->previousRequest;
    }

    /**
     * Set draft
     *
     * @param \Uzink\BackendBundle\Entity\Draft $draft
     *
     * @return Request
     */
    public function setDraft(\Uzink\BackendBundle\Entity\Draft $draft = null)
    {
        $this->draft = $draft;
    
        return $this;
    }

    /**
     * Get draft
     *
     * @return \Uzink\BackendBundle\Entity\Draft
     */
    public function getDraft()
    {
        return $this->draft;
    }
}
