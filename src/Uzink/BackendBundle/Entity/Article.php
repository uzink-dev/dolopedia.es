<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Uzink\UtilsBundle\Utils\SEOsable;

use JMS\Serializer\Annotation as JMS;

/**
 * Article
 *
 * @ORM\Entity(repositoryClass="Uzink\BackendBundle\Entity\ArticleRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Article extends BaseArticle
{
    use SEOsable;

    const ITEMS_PER_PAGE = 3;

    const ACTION_SAVE = 'save';
    const ACTION_SAVE_AND_EXIT = 'save-and-exit';
    const ACTION_REVISE = 'revise';
    const ACTION_VALIDATE = 'validate';
    const ACTION_NO_VALIDATE = 'no-validate';
    const ACTION_PUBLISH = 'publish';
    const ACTION_NO_PUBLISH = 'no-publish';
    const ACTION_DELETE = 'delete';
    const ACTION_PREVIEW = 'preview';

    const PARAM_ASSIGNED_ARTICLES = 'asignados';
    const PARAM_COLLABORATIONS = 'colaboraciones';

    public function __construct() {
        parent::__construct();

        $this->drafts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
        $this->published = false;
    }

    public function __toString() {
        if (is_string($this->title)) return $this->title;
        else return 'Artículo ID ' . $this->id;
    }

    public static function getSharpCategories() {
        $choices = array();

        $choices['postoperativePain'] = 'crossCategory.sharp.postoperativePain';
        $choices['obstetricPain'] = 'crossCategory.sharp.obstetricPain';
        $choices['polytrauma'] = 'crossCategory.sharp.polytrauma';
        $choices['majorBurns'] = 'crossCategory.sharp.majorBurns';
        $choices['symptomaticPain'] = 'crossCategory.sharp.symptomaticPain';

        return $choices;
    }

    public static function getChronicCategories() {
        $choices = array();

        $choices['physicalExploration'] = array();
        $choices['physicalExploration']['headFaceMouth'] =  'crossCategory.chronic.physicalExploration.headFaceMouth';
        $choices['physicalExploration']['neck'] =           'crossCategory.chronic.physicalExploration.neck';
        $choices['physicalExploration']['superiorLimbs'] =  'crossCategory.chronic.physicalExploration.superiorLimbs';
        $choices['physicalExploration']['thorax'] =         'crossCategory.chronic.physicalExploration.thorax';
        $choices['physicalExploration']['abdominal'] =      'crossCategory.chronic.physicalExploration.abdominal';
        $choices['physicalExploration']['lumbarRegion'] =   'crossCategory.chronic.physicalExploration.lumbarRegion';
        $choices['physicalExploration']['lowerLimbs'] =     'crossCategory.chronic.physicalExploration.lowerLimbs';
        $choices['physicalExploration']['pelvicRegion'] =   'crossCategory.chronic.physicalExploration.pelvicRegion';
        $choices['physicalExploration']['genitalRegion'] =  'crossCategory.chronic.physicalExploration.genitalRegion';
        $choices['physicalExploration']['general'] =        'crossCategory.chronic.physicalExploration.general';

        $choices['furtherExploration'] = array();
        $choices['furtherExploration']['headFaceMouth'] =   'crossCategory.chronic.furtherExploration.headFaceMouth';
        $choices['furtherExploration']['neck'] =            'crossCategory.chronic.furtherExploration.neck';
        $choices['furtherExploration']['superiorLimbs'] =   'crossCategory.chronic.furtherExploration.superiorLimbs';
        $choices['furtherExploration']['thorax'] =          'crossCategory.chronic.furtherExploration.thorax';
        $choices['furtherExploration']['abdominal'] =       'crossCategory.chronic.furtherExploration.abdominal';
        $choices['furtherExploration']['lumbarRegion'] =    'crossCategory.chronic.furtherExploration.lumbarRegion';
        $choices['furtherExploration']['lowerLimbs'] =      'crossCategory.chronic.furtherExploration.lowerLimbs';
        $choices['furtherExploration']['pelvicRegion'] =    'crossCategory.chronic.furtherExploration.pelvicRegion';
        $choices['furtherExploration']['genitalRegion'] =   'crossCategory.chronic.furtherExploration.genitalRegion';
        $choices['furtherExploration']['general'] =         'crossCategory.chronic.furtherExploration.general';

        $choices['clinicalEntities'] = array();
        $choices['clinicalEntities']['headFaceMouth'] =     'crossCategory.chronic.clinicalEntities.headFaceMouth';
        $choices['clinicalEntities']['neck'] =              'crossCategory.chronic.clinicalEntities.neck';
        $choices['clinicalEntities']['superiorLimbs'] =     'crossCategory.chronic.clinicalEntities.superiorLimbs';
        $choices['clinicalEntities']['thorax'] =            'crossCategory.chronic.clinicalEntities.thorax';
        $choices['clinicalEntities']['abdominal'] =         'crossCategory.chronic.clinicalEntities.abdominal';
        $choices['clinicalEntities']['lumbarRegion'] =      'crossCategory.chronic.clinicalEntities.lumbarRegion';
        $choices['clinicalEntities']['lowerLimbs'] =        'crossCategory.chronic.clinicalEntities.lowerLimbs';
        $choices['clinicalEntities']['pelvicRegion'] =      'crossCategory.chronic.clinicalEntities.pelvicRegion';
        $choices['clinicalEntities']['genitalRegion'] =     'crossCategory.chronic.clinicalEntities.genitalRegion';
        $choices['clinicalEntities']['general'] =           'crossCategory.chronic.clinicalEntities.general';
        
        $choices['pharmacologicalProtocols'] = array();
        $choices['pharmacologicalProtocols']['headFaceMouth'] =     'crossCategory.chronic.pharmacologicalProtocols.headFaceMouth';
        $choices['pharmacologicalProtocols']['neck'] =              'crossCategory.chronic.pharmacologicalProtocols.neck';
        $choices['pharmacologicalProtocols']['superiorLimbs'] =     'crossCategory.chronic.pharmacologicalProtocols.superiorLimbs';
        $choices['pharmacologicalProtocols']['thorax'] =            'crossCategory.chronic.pharmacologicalProtocols.thorax';
        $choices['pharmacologicalProtocols']['abdominal'] =         'crossCategory.chronic.pharmacologicalProtocols.abdominal';
        $choices['pharmacologicalProtocols']['lumbarRegion'] =      'crossCategory.chronic.pharmacologicalProtocols.lumbarRegion';
        $choices['pharmacologicalProtocols']['lowerLimbs'] =        'crossCategory.chronic.pharmacologicalProtocols.lowerLimbs';
        $choices['pharmacologicalProtocols']['pelvicRegion'] =      'crossCategory.chronic.pharmacologicalProtocols.pelvicRegion';
        $choices['pharmacologicalProtocols']['genitalRegion'] =     'crossCategory.chronic.pharmacologicalProtocols.genitalRegion';
        $choices['pharmacologicalProtocols']['general'] =           'crossCategory.chronic.pharmacologicalProtocols.general';
        
        $choices['painProcedures'] = array();
        $choices['painProcedures']['headFaceMouth'] =     'crossCategory.chronic.painProcedures.headFaceMouth';
        $choices['painProcedures']['neck'] =              'crossCategory.chronic.painProcedures.neck';
        $choices['painProcedures']['superiorLimbs'] =     'crossCategory.chronic.painProcedures.superiorLimbs';
        $choices['painProcedures']['thorax'] =            'crossCategory.chronic.painProcedures.thorax';
        $choices['painProcedures']['abdominal'] =         'crossCategory.chronic.painProcedures.abdominal';
        $choices['painProcedures']['lumbarRegion'] =      'crossCategory.chronic.painProcedures.lumbarRegion';
        $choices['painProcedures']['lowerLimbs'] =        'crossCategory.chronic.painProcedures.lowerLimbs';
        $choices['painProcedures']['pelvicRegion'] =      'crossCategory.chronic.painProcedures.pelvicRegion';
        $choices['painProcedures']['genitalRegion'] =     'crossCategory.chronic.painProcedures.genitalRegion';
        $choices['painProcedures']['general'] =           'crossCategory.chronic.painProcedures.general';
        
        $choices['rehabilitationExercises'] = array();
        $choices['rehabilitationExercises']['headFaceMouth'] =     'crossCategory.chronic.rehabilitationExercises.headFaceMouth';
        $choices['rehabilitationExercises']['neck'] =              'crossCategory.chronic.rehabilitationExercises.neck';
        $choices['rehabilitationExercises']['superiorLimbs'] =     'crossCategory.chronic.rehabilitationExercises.superiorLimbs';
        $choices['rehabilitationExercises']['thorax'] =            'crossCategory.chronic.rehabilitationExercises.thorax';
        $choices['rehabilitationExercises']['abdominal'] =         'crossCategory.chronic.rehabilitationExercises.abdominal';
        $choices['rehabilitationExercises']['lumbarRegion'] =      'crossCategory.chronic.rehabilitationExercises.lumbarRegion';
        $choices['rehabilitationExercises']['lowerLimbs'] =        'crossCategory.chronic.rehabilitationExercises.lowerLimbs';
        $choices['rehabilitationExercises']['pelvicRegion'] =      'crossCategory.chronic.rehabilitationExercises.pelvicRegion';
        $choices['rehabilitationExercises']['genitalRegion'] =     'crossCategory.chronic.rehabilitationExercises.genitalRegion';
        $choices['rehabilitationExercises']['general'] =           'crossCategory.chronic.rehabilitationExercises.general';        

        return $choices;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="articles")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\NotBlank()
     * @JMS\Expose
     * @var Category
     */
    protected $category;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $supervisor;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assignedArticles")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $editor;

    /**
     * @var Comment
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article", cascade={"remove"})
     */
    protected $comments;

    /**
     * @var Rating
     *
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="article", cascade={"remove"})
     */
    protected $ratings;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    protected $published;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * @var User $publishedBy
     *
     * @Gedmo\Blameable(on="change", field={"publishedAt"})
     * @ORM\ManyToOne(targetEntity="Uzink\BackendBundle\Entity\User")
     * @ORM\JoinColumn(name="published_by", referencedColumnName="id")
     */
    private $publishedBy;

    /**
     * @var \Uzink\BackendBundle\Entity\Request
     *
     * @ORM\OneToMany(targetEntity="Request", mappedBy="article", cascade={"remove"})
     */
    protected $request;

    /**
     * @var \Uzink\BackendBundle\Entity\Draft
     *
     * @ORM\OneToMany(targetEntity="Draft", mappedBy="article", cascade={"remove"})
     * @JMS\Exclude
     */
    protected $drafts;

    /**
     * @var \Uzink\BackendBundle\Entity\User
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="collaborations")
     * @JMS\Exclude
     */
    protected $collaborators;

    /**
     * @var boolean
     * @ORM\Column(name="without_ads", type="boolean")
     */
    protected $whitoutAds = false;

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

    public function fillFromDraft(Draft $draft) {
        $this->title        = $draft->getTitle();
        $this->category     = $draft->getCategory();
        $this->type         = $draft->getType();
        $this->introduction = $draft->getIntroduction();
        $this->content      = $draft->getContent();
        foreach ($draft->getBibliographicEntries() as $bEntry) {
            if (!$this->hasBibliographicEntry($bEntry)) $this->addBibliographicEntry($bEntry);
        }
        $this->attached     = $draft->getAttached();
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Article
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
     * Set category
     *
     * @param \Uzink\BackendBundle\Entity\Category $category
     *
     * @return Article
     */
    public function setCategory($category)
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
     * Set type
     *
     * @param string $type
     *
     * @return Article
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
     * Set editor
     *
     * @param \Uzink\BackendBundle\Entity\User $editor
     *
     * @return Article
     */
    public function setEditor(User $editor = null)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get editor
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set supervisor
     *
     * @param \Uzink\BackendBundle\Entity\User $supervisor
     *
     * @return BaseArticle
     */
    public function setSupervisor(User $supervisor = null)
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    /**
     * Get supervisor
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * Set owner
     *
     * @param \Uzink\BackendBundle\Entity\User $owner
     *
     * @return Article
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
     * Add comments
     *
     * @param \Uzink\BackendBundle\Entity\Comment $comments
     *
     * @return Article
     */
    public function addComment(Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Uzink\BackendBundle\Entity\Comment $comments
     */
    public function removeComment(Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @param string $scope
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getComments($scope = Comment::SCOPE_PUBLIC)
    {
        $comments = new ArrayCollection();

        foreach($this->comments as $comment) {
            if ($comment->getScope() == $scope) $comments->add($comment);
        }

        return $comments;
    }

    public function getDiscussions()
    {
        return $this->getComments(Comment::SCOPE_DISCUSSION);
    }

    /**
     * Add ratings
     *
     * @param \Uzink\BackendBundle\Entity\Rating $ratings
     *
     * @return Article
     */
    public function addRating(Rating $ratings)
    {
        $this->ratings[] = $ratings;

        return $this;
    }

    /**
     * Remove ratings
     *
     * @param \Uzink\BackendBundle\Entity\Rating $ratings
     */
    public function removeRating(Rating $ratings)
    {
        $this->ratings->removeElement($ratings);
    }

    /**
     * Get ratings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    public function getRate() {
        $ratings = $this->ratings;
        $rate = 0;

        if (count($ratings) > 0) {
            foreach($ratings as $currentRate) {
                $rate += $currentRate->getRating();
            }

            $rate = $rate / count($ratings);
            $rate = round($rate * 2) / 2;
        }

        return $rate;
    }

    public function getRateByUser(User $user) {
        $ratings = $this->ratings;
        $rate = $this->getRate();

        foreach($ratings as $currentRate) {
            if($currentRate->getOwner() == $user) return $currentRate->getRating();
        }

        return $rate;
    }

    /**
     * Set publishedAt
     *
     * @return \Datetime
     */
    public function getPublishedAt() {
        return $this->publishedAt;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Article
     */
    public function setPublished($published)
    {
        if ($published) $this->publishedAt = new \DateTime('now');
        else $this->publishedAt = null;

        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set request
     *
     * @param \Uzink\BackendBundle\Entity\Request $request
     *
     * @return Article
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    
        return $this;
    }

    /**
     * Get request
     *
     * @return \Uzink\BackendBundle\Entity\Request
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * Add request
     *
     * @param \Uzink\BackendBundle\Entity\Request $request
     *
     * @return Article
     */
    public function addRequest(Request $request)
    {
        $this->request[] = $request;
    
        return $this;
    }

    /**
     * Remove request
     *
     * @param \Uzink\BackendBundle\Entity\Request $request
     */
    public function removeRequest(Request $request)
    {
        $this->request->removeElement($request);
    }

    /**
     * Set drafts
     *
     * @param \Uzink\BackendBundle\Entity\draft $drafts
     *
     * @return Draft
     */
    public function setDrafts($drafts)
    {
        $this->drafts = $drafts;

        return $this;
    }

    /**
     * Get drafts
     *
     * @return \Uzink\BackendBundle\Entity\Draft
     */
    public function getDrafts()
    {
        return $this->drafts;
    }


    /**
     * Add draft
     *
     * @param \Uzink\BackendBundle\Entity\Draft $draft
     *
     * @return Draft
     */
    public function addDraft(Draft $draft)
    {
        $this->drafts[] = $draft;

        return $this;
    }

    /**
     * Remove draft
     *
     * @param \Uzink\BackendBundle\Entity\Draft $draft
     */
    public function removeDraft(Draft $draft)
    {
        $this->drafts->removeElement($draft);
    }    

    public function getLastDraft()
    {
        $drafts = $this->drafts;
        $lastDraft = null;

        if ($drafts->count() > 0) $lastDraft = $drafts->first();

        foreach ($drafts as $draft) {
            if ($lastDraft->getCreatedAt() < $draft->getCreatedAt()) $lastDraft = $draft;
        }

        return $lastDraft;
    }

    /**
     * Set publishedBy.
    
     *
     * @param \Uzink\BackendBundle\Entity\User $publishedBy
     *
     * @return Article
     */
    public function setPublishedBy(User $publishedBy = null)
    {
        $this->publishedBy = $publishedBy;
    
        return $this;
    }

    /**
     * Get publishedBy.
    
     *
     * @return \Uzink\BackendBundle\Entity\User
     */
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }

    /**
     * Add collaborator.
    
     *
     * @param \Uzink\BackendBundle\Entity\User $collaborator
     *
     * @return Article
     */
    public function addCollaborators(User $collaborator)
    {
        if (!in_array($collaborator, $this->collaborators->toArray())) $this->collaborators[] = $collaborator;

        return $this;
    }

    /**
     * Remove collaborator.
    
     *
     * @param \Uzink\BackendBundle\Entity\User $collaborator
     */
    public function removeCollaborator(User $collaborator)
    {
        $this->collaborators->removeElement($collaborator);
    }

    /**
     * Get collaborations.
    
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * @return boolean
     */
    public function isWhitoutAds()
    {
        return $this->whitoutAds;
    }

    /**
     * @param boolean $whitoutAds
     *
     * @return $this
     */
    public function setWhitoutAds($whitoutAds)
    {
        $this->whitoutAds = $whitoutAds;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdHeaderZone()
    {
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

    public function getBreadcrumbs()
    {
        $breadcrumbs = $this->category->getBreadcrumbs();
        $breadcrumbs->add($this);

        return $breadcrumbs;
    }
}
