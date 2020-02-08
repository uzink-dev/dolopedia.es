<?php

namespace Uzink\BackendBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Uzink\UtilsBundle\Utils\SEOsable;
use Gedmo\Mapping\Annotation as Gedmo;
use Uzink\BackendBundle\Validator\Constraints as CustomAssert;

use JMS\Serializer\Annotation as JMS;

/**
 * Category
 *
 * @ORM\Table("category")
 * @ORM\Entity(repositoryClass="Uzink\BackendBundle\Entity\CategoryRepository")
 * @Vich\Uploadable
 * @Gedmo\Tree(type="nested")
 * @JMS\ExclusionPolicy("all")
 * @CustomAssert\NotContainsAnotherLeaders
 */
class Category
{
    use SEOsable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="blob", nullable=true)
     */
    private $introduction;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="blob", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, name="image", nullable=true)
     *
     * @var string $image
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="category_image", fileNameProperty="image")
     *
     * @var File $imageResource
     */
    private $imageResource;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="categories")
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Assert\NotBlank()
     */
    private $owner;

    /**
     * @var Article
     * 
     * @ORM\OneToMany(targetEntity="Article", mappedBy="category")
     */
    private $articles;

    /**
     * @var BibliographicEntry
     *
     * @ORM\ManyToMany(targetEntity="BibliographicEntry", cascade={"persist", "refresh"})
     */
    protected $bibliographicEntries;
    
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
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @var Category
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"persist"})
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var string
     * @ORM\Column(name="ad_header_zone", type="string", length=255, nullable=true)
     */
    private $adHeaderZone;

    /**
     * @var string
     * @ORM\Column(name="ad_side_zone", type="string", length=255, nullable=true)
     */
    private $adSideZone;

    /**
     * @var string
     * @ORM\Column(name="ad_footer_zone", type="string", length=255, nullable=true)
     */
    private $adFooterZone;

    /**
     * @var CategoryLink
     *
     * @ORM\OneToMany(targetEntity="Uzink\BackendBundle\Entity\CategoryLink", mappedBy="category", cascade={"all"})
     */
    private $categoryLinks;

    /**
     * Constructor
     * @param Category $masterCategory
     */
    public function __construct(Category $masterCategory = null)
    {
        $this->articles = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->bibliographicEntries = new ArrayCollection();

        if ($masterCategory) {
            $this->title = $masterCategory->getTitle();
            $this->description = $masterCategory->getDescription();
            $this->image = $masterCategory->getImage();
            $this->owner = $masterCategory->getOwner();
            $this->adHeaderZone = $masterCategory->getAdHeaderZone();
            $this->adSideZone = $masterCategory->getAdSideZone();
            $this->adFooterZone = $masterCategory->getAdFooterZone();
            foreach($masterCategory->getChildren() as $child) {
                $auxCategory = new Category($child);
                $auxCategory->setParent($this);
                $this->children->add($auxCategory);
            }
        }
    }

    public function __clone()
    {
        $this->id = null;
        $this->articles = new ArrayCollection();
        $categories = new ArrayCollection();
        foreach($this->children as $category) {
            $childCategory = clone $category;
            $categories->add($childCategory);
        }
    }

    public function __toString()
    {
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
     * @param mixed $lvl
     * @return $this
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Category
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
     * Set introduction
     *
     * @param string $introduction
     *
     * @return Category
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
     * Set description
     *
     * @param string $description
     *
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->getResource($this->description);
    }

    /**
     * Add articles
     *
     * @param \Uzink\BackendBundle\Entity\Article $articles
     *
     * @return Category
     */
    public function addArticle(Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    /**
     * Remove articles
     *
     * @param \Uzink\BackendBundle\Entity\Article $articles
     */
    public function removeArticle(Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getArticles()
    {
        if ($this->articles->count() > 0) {
            $iterator = $this->articles->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
            });
            $collection = new ArrayCollection(iterator_to_array($iterator));

            return $collection;
        } else {
            return $this->articles;
        }
    }

    public function getPublishedArticles()
    {
        $publishedArticles = array();

        foreach($this->articles as $article) {
            if ($article->getPublished()) $publishedArticles[] = $article;
        }

        return $publishedArticles;
    }

    public function countArticles()
    {
        $articles = 0;
        $articles += count($this->getArticles());

        foreach ($this->getChildren() as $child) {
            $articles += $child->countArticles();
        }

        return $articles;
    }

    public function countPublishedArticles()
    {
        $articles = 0;
        $articles += count($this->getPublishedArticles());

        foreach ($this->getChildren() as $child) {
            $articles += $child->countPublishedArticles();
        }

        return $articles;
    }

    /**
     * Add bibliographicEntries
     *
     * @param \Uzink\BackendBundle\Entity\BibliographicEntry $bibliographicEntries
     *
     * @return Category
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

    public function getBreadcrumbs()
    {
        $breadcrumbs = new ArrayCollection();

        if ($this->parent) {
            $breadcrumbs = $this->parent->getBreadcrumbs();
        }

        $breadcrumbs->add($this);

        return $breadcrumbs;
    }

    /**
     * Set owner
     *
     * @param \Uzink\BackendBundle\Entity\User $owner
     *
     * @return Category
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
     * Check if a child category
     *
     * @param User $user
     * @return boolean
     */
    public function hasChildDifferentOwner(User $user = null)
    {
        if (!$user) $user = $this->owner;
        $differentOwner = false;

        if ($user != $this->owner) $differentOwner = $differentOwner || true;

        foreach ($this->getChildren() as $child) {
            $differentOwner = $differentOwner || $child->hasChildDifferentOwner($user);
        }

        return $differentOwner;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Category
     */
    public function setImage($image)
    {
        $this->image = $image;
    
        return $this;
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

    public function setImageResource(File $imageResource)
    {
        $this->imageResource = $imageResource;
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @return File
     */
    public function getImageResource()
    {
        return $this->imageResource;
    }

    private function getResource($resource) {
        if (is_string($resource)) {
            return $resource;
        }elseif ($resource != null) {
            rewind($resource);
            return stream_get_contents($resource);
        } else {
            return null;
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

    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function getAdHeaderZone()
    {
        if ($this->adHeaderZone) return $this->adHeaderZone;
        elseif ($this->getParent()) return $this->getParent()->getAdHeaderZone();

        return $this->adHeaderZone;
    }

    /**
     * @param string $adHeaderZone
     *
     * @return $this
     */
    public function setAdHeaderZone($adHeaderZone)
    {
        $this->adHeaderZone = $adHeaderZone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdSideZone()
    {
        if ($this->adSideZone) return $this->adSideZone;
        elseif ($this->getParent()) return $this->getParent()->getAdSideZone();

        return $this->adSideZone;
    }

    /**
     * @param string $adSideZone
     *
     * @return $this
     */
    public function setAdSideZone($adSideZone)
    {
        $this->adSideZone = $adSideZone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdFooterZone()
    {
        if ($this->adFooterZone) return $this->adFooterZone;
        elseif ($this->getParent()) return $this->getParent()->getAdFooterZone();

        return $this->adFooterZone;
    }

    /**
     * @param string $adFooterZone
     *
     * @return $this
     */
    public function setAdFooterZone($adFooterZone)
    {
        $this->adFooterZone = $adFooterZone;

        return $this;
    }
}
