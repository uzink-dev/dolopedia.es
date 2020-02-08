<?php
namespace Uzink\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Uzink\UtilsBundle\Utils\SEOsable;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
/**
 * Base Article
 *
 * @ORM\Table("article")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="entity_type", type="string")
 * @ORM\DiscriminatorMap({"draft" = "Draft", "article" = "Article"})
 * @JMS\ExclusionPolicy("all")
 *
 */
abstract class BaseArticle
{
    const TYPE_TECHNIQUES       = 'techniques';
    const TYPE_CLINIC_ENTITY    = 'clinicEntity';
    const TYPE_GENERAL          = 'general';
    const TYPE_DRUG             = 'drug';

    public function __construct() {
        $this->bibliographicEntries = new ArrayCollection();
        $this->position = 2147483647;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="blob", nullable=true)
     */
    protected $introduction;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="array", nullable=true)
     */
    protected $content;

    /**
     * @var BibliographicEntry
     *
     * @ORM\ManyToMany(targetEntity="BibliographicEntry", cascade={"persist", "refresh"})
     */
    protected $bibliographicEntries;

    /**
     * @var string
     *
     * @ORM\Column(name="attached", type="text", nullable=true)
     */
    protected $attached;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Uzink\BackendBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     * @JMS\Expose
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     * @JMS\Expose
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
     * Set content
     *
     * @param array $content
     *
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return array
     */
    public function getContent()
    {
        return $this->getResource($this->content);
    }

    public function getRawContent()
    {
        if ($this->content) return implode('.', $this->content);
        return '';
    }

    /**
     * Set introduction
     *
     * @param string $introduction
     *
     * @return Article
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->getResource($this->introduction);
    }

    /**
     * Set attached
     *
     * @param string $attached
     *
     * @return Article
     */
    public function setAttached($attached)
    {
        $this->attached = $attached;

        return $this;
    }

    /**
     * Get attached
     *
     * @return string
     */
    public function getAttached()
    {
        return $this->getResource($this->attached);
    }

    protected function getResource($resource) {
        if ($resource != null && is_resource($resource)) {
            rewind($resource);
            return stream_get_contents($resource);
        } else {
            return $resource;
        }
    }

    /**
     * Add bibliographicEntries
     *
     * @param \Uzink\BackendBundle\Entity\BibliographicEntry $bibliographicEntries
     *
     * @return Article
     */
    public function addBibliographicEntry(BibliographicEntry $bibliographicEntries)
    {
        $this->bibliographicEntries->add($bibliographicEntries);

        return $this;
    }

    /**
     * Remove bibliographicEntries
     *
     * @param \Uzink\BackendBundle\Entity\BibliographicEntry $bibliographicEntries
     */
    public function removeBibliographicEntry(BibliographicEntry $bibliographicEntries)
    {
        $this->bibliographicEntries->removeElement($bibliographicEntries);
    }

    /**
     * Has bibliographicEntries
     *
     * @param BibliographicEntry $bibliographicEntry
     * @return bool
     */
    public function hasBibliographicEntry(BibliographicEntry $bibliographicEntry)
    {
        foreach ($this->bibliographicEntries as $currentBibliographicEntry) {
            if ($currentBibliographicEntry == $bibliographicEntry) return true;
        }

        return false;
    }

    /**
     * Get bibliographicEntries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBibliographicEntries()
    {
        if ($this->bibliographicEntries->count() > 0) {
            $iterator = $this->bibliographicEntries->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
            });
            $collection = new ArrayCollection(iterator_to_array($iterator));

            return $collection;
        } else {
            return $this->bibliographicEntries;
        }
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Article
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
     * Set status
     *
     * @param string $status
     *
     * @return BaseArticle
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

    /**
     * Set createdBy
     *
     * @param \Uzink\BackendBundle\Entity\User $createdBy
     *
     * @return BaseArticle
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;
    
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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
     * Set createdAt.
    
     *
     * @param \DateTime $createdAt
     *
     * @return BaseArticle
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Set updatedAt.
    
     *
     * @param \DateTime $updatedAt
     *
     * @return BaseArticle
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }
}
