<?php

namespace Uzink\BackendBundle\Entity;

use Azine\EmailBundle\Entity\RecipientInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\ResourceBundle\Util\ArrayAccessibleResourceBundle;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use FOS\UserBundle\Model\User as FosUser;
use Uzink\BackendBundle\Behaviours as Behaviours;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Table("user")
 * @ORM\Entity(repositoryClass="Uzink\BackendBundle\Entity\UserRepository")
 * @Vich\Uploadable
 * @Gedmo\Tree(type="nested")
 *
 */
class User extends FosUser implements RecipientInterface
{
    use ORMBehaviors\Timestampable\Timestampable;
    use Behaviours\Tree;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_SUPERVISOR = 'ROLE_SUPERVISOR';
    const ROLE_LEADER = 'ROLE_LEADER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const SOCIAL_FACEBOOK = 'facebook';
    const SOCIAL_TWITTER = 'twitter';
    const SOCIAL_YOUTUBE = 'youtube';
    const SOCIAL_LINKEDIN = 'linkedin';

    private static $roleHierachy = array(
     // Role                   Legacy Roles
        self::ROLE_USER         => array(),
        self::ROLE_EDITOR       => array(self::ROLE_USER),
        self::ROLE_SUPERVISOR   => array(self::ROLE_EDITOR),
        self::ROLE_LEADER       => array(self::ROLE_SUPERVISOR),
        self::ROLE_ADMIN        => array(self::ROLE_LEADER),
        self::ROLE_SUPER_ADMIN  => array(self::ROLE_ADMIN)
    );

    //<editor-fold desc="Properties">
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, name="image", nullable=true)
     *
     * @var string $image
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="image")
     *
     * @var File $imageResource
     */
    private $imageResource;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var array
     *
     * @ORM\Column(name="interests", type="array", nullable=true)
     */
    private $interests;

    /**
     * @var string
     *
     * @ORM\Column(name="cv", type="text", nullable=true)
     */
    private $cv;

    /**
     * @var array
     *
     * @ORM\Column(name="socialProfiles", type="array", nullable=true)
     */
    private $socialProfiles; 

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname1", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $surname1;

    /**
     * @var string
     *
     * @ORM\Column(name="surname2", type="string", length=255, nullable=true)
     */
    private $surname2;

    /**
     * @var string
     *
     * @ORM\Column(name="sector", type="string", length=255, nullable=true)
     */
    private $sector;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country = 'ES';

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=255, nullable=true)
     */
    private $timezone = 'GMT+01';

    /**
     * @var string
     *
     * @ORM\Column(name="job", type="string", length=255, nullable=true)
     */
    private $job;

    /**
     * @ORM\ManyToOne(targetEntity="Center", inversedBy="users")
     * @ORM\JoinColumn(name="workplace_id", referencedColumnName="id", nullable=true)
     */
    private $workplace;

    /**
     * @var string
     *
     * @ORM\Column(name="otherWorkplace", type="string", length=255, nullable=true)
     */
    private $otherWorkplace; 

    /**
     * @var User
     * 
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="contact_users")
     */
    private $contacts;

    /**
     * @var User
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="contacts")
     * @ORM\JoinTable(name="contact_users")
     */
    private $followers;

    /**
     * @var Comment
     * 
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="owner") 
     */
    private $comments;

    /**
     * @var Activity
     *
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="receiver")
     */    
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="receiver")
     */
    private $receivedMessages;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="sender")
     */    
    private $sentMessages;

    /**
     * @ORM\ManyToMany(targetEntity="Article")
     * @ORM\JoinTable(name="favourites_articles")
     */
    private $favouritesArticles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Request", mappedBy="userTo")
     */
    private $requests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Request", mappedBy="userFrom")
     */
    private $sentRequests;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="collaborators")
     */
    private $collaborations;

    /**
     * @var Article
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="editor")
     */
    private $assignedArticles;

    /**
     * @var Category
     * 
     * @ORM\OneToMany(targetEntity="Category", mappedBy="owner")
     */
    private $categories;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="twitter_id", type="string", length=255, nullable=true) */
    protected $twitter_id;

    /** @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true) */
    protected $twitter_access_token;
    //</editor-fold>

    //<editor-fold desc="Primitive Methods">
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->activities = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->assignedArticles = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->collaborations = new ArrayCollection();
        $this->favouritesArticles = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->sentRequests = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->socialProfiles = array(
            self::SOCIAL_FACEBOOK => '',
            self::SOCIAL_TWITTER => '',
            self::SOCIAL_YOUTUBE => '',
            self::SOCIAL_LINKEDIN => ''
        );
    }
    
    public function __toString() {
        $returnedName = '';

        if (!empty($this->name)) $returnedName .= $this->name . ' ';
        if (!empty($this->surname1)) $returnedName .= $this->surname1 . ' ';
        if (!empty($this->surname2)) $returnedName .= $this->surname2 . ' ';

        return trim($returnedName);
    }
    //</editor-fold>

    //<editor-fold desc="Getters/Setters">
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()     {
        return $this->id;
    }

    public function getFullName()
    {
        return $this->__toString();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)     {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()     {
        return $this->name;
    }

    /**
     * Set surname1
     *
     * @param string $surname1
     *
     * @return User
     */
    public function setSurname1($surname1)     {
        $this->surname1 = $surname1;

        return $this;
    }

    /**
     * Get surname1
     *
     * @return string 
     */
    public function getSurname1()     {
        return $this->surname1;
    }

    /**
     * Set surname2
     *
     * @param string $surname2
     *
     * @return User
     */
    public function setSurname2($surname2)     {
        $this->surname2 = $surname2;

        return $this;
    }

    /**
     * Get surname2
     *
     * @return string 
     */
    public function getSurname2()     {
        return $this->surname2;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return User
     */
    public function setCountry($country)     {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()     {
        return $this->country;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     *
     * @return User
     */
    public function setTimezone($timezone)     {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()     {
        return $this->timezone;
    }

    /**
     * Set job
     *
     * @param string $job
     *
     * @return User
     */
    public function setJob($job)     {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return string 
     */
    public function getJob()     {
        return $this->job;
    }

    /**
     * Set workplace
     *
     * @param \stdClass $workplace
     *
     * @return User
     */
    public function setWorkplace($workplace)     {
        $this->workplace = $workplace;

        return $this;
    }

    /**
     * Get workplace
     *
     * @return \stdClass 
     */
    public function getWorkplace()     {
        return $this->workplace;
    }

    /**
     * Set otherWorkplace
     *
     * @param string $otherWorkplace
     *
     * @return User
     */
    public function setOtherWorkplace($otherWorkplace)     {
        $this->otherWorkplace = $otherWorkplace;

        return $this;
    }

    /**
     * Get otherWorkplace
     *
     * @return string 
     */
    public function getOtherWorkplace()     {
        return $this->otherWorkplace;
    }

    /**
     * Add comments
     *
     * @param \Uzink\BackendBundle\Entity\Comment $comments
     *
     * @return User
     */
    public function addComment(Comment $comments)     {
        $this->comments[] = $comments;


        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Uzink\BackendBundle\Entity\Comment $comments
     */
    public function removeComment(Comment $comments)     {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()     {
        return $this->comments;
    }

    /**
     * Add favouritesArticles
     *
     * @param \Uzink\BackendBundle\Entity\Article $favouritesArticles
     *
     * @return User
     */
    public function addFavouritesArticle(Article $favouritesArticles)     {
        $this->favouritesArticles[] = $favouritesArticles;


        return $this;
    }

    /**
     * Remove favouritesArticles
     *
     * @param \Uzink\BackendBundle\Entity\Article $favouritesArticles
     */
    public function removeFavouritesArticle(Article $favouritesArticles)     {
        $this->favouritesArticles->removeElement($favouritesArticles);
    }

    /**
     * Get favouritesArticles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavouritesArticles() {
        $articles = $this->favouritesArticles;

        $publishedArticles = new ArrayCollection();
        foreach($articles as $currentArticle) {
            if ($currentArticle->getPublished()) $publishedArticles->add($currentArticle);
        }

        return $publishedArticles;
    }

    public function isFavouriteArticle(Article $article) {
        foreach ($this->favouritesArticles as $currentArticle) {
            if ($currentArticle->getId() == $article->getId()) return true;
        }

        return false;
    }

    /**
     * Set sector
     *
     * @param string $sector
     *
     * @return User
     */
    public function setSector($sector)     {
        $this->sector = $sector;


        return $this;
    }

    /**
     * Get sector
     *
     * @return string
     */
    public function getSector()     {
        return $this->sector;
    }

    /**
     * Set interests
     *
     * @param array $interests
     *
     * @return User
     */
    public function setInterests($interests)     {
        $this->interests = $interests;


        return $this;
    }

    /**
     * Get interests
     *
     * @return array
     */
    public function getInterests()     {
        return $this->interests;
    }

    /**
     * Set cv
     *
     * @param string $cv
     *
     * @return User
     */
    public function setCv($cv)     {
        $this->cv = $cv;


        return $this;
    }

    /**
     * Get cv
     *
     * @return string
     */
    public function getCv()     {
        return $this->cv;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)     {
        $this->address = $address;


        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()     {
        return $this->address;
    }



    public function setEmail($email) {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);
    }

    //<editor-fold desc="Contacts">
    /**
     * Add contacts
     *
     * @param User $contacts
     *
     * @return User
     */
    public function addContact(User $contacts)     {
        $this->contacts[] = $contacts;


        return $this;
    }

    /**
     * Remove contacts
     *
     * @param User $contacts
     */
    public function removeContact(User $contacts)     {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return ArrayCollection
     */
    public function getContacts()     {
        return $this->contacts;
    }

    /**
     * Add follower
     *
     * @param \Uzink\BackendBundle\Entity\User $follower
     *
     * @return User
     */
    public function addFollower(User $follower)
    {
        $this->followers[] = $follower;

        return $this;
    }

    /**
     * Remove follower
     *
     * @param \Uzink\BackendBundle\Entity\User $follower
     */
    public function removeFollower(User $follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }
    //</editor-fold>

    /**
     * Add assignedArticles
     *
     * @param \Uzink\BackendBundle\Entity\Article $assignedArticles
     *
     * @return User
     */
    public function addAssignedArticle(Article $assignedArticles)     {
        $this->assignedArticles[] = $assignedArticles;

        return $this;
    }

    /**
     * Remove assignedArticles
     *
     * @param \Uzink\BackendBundle\Entity\Article $assignedArticles
     */
    public function removeAssignedArticle(Article $assignedArticles)     {
        $this->assignedArticles->removeElement($assignedArticles);
    }

    /**
     * Get assignedArticles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssignedArticles()     {
        return $this->assignedArticles;
    }

    /**
     * Add categories
     *
     * @param \Uzink\BackendBundle\Entity\Category $categories
     *
     * @return User
     */
    public function addCategory(Category $categories)     {
        $this->categories[] = $categories;


        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Uzink\BackendBundle\Entity\Category $categories
     */
    public function removeCategory(Category $categories)     {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()     {
        return $this->categories;
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
        $this->setUpdatedAt(new \DateTime('now'));
    }

    /**
     * @return File
     */
    public function getImageResource()
    {
        return $this->imageResource;
    }


    /**
     * Add request
     *
     * @param Request $request
     *
     * @return User
     */
    public function addRequest(Request $request)
    {
        $this->requests[] = $request;

        return $this;
    }

    /**
     * Remove request
     *
     * @param Request $request
     */
    public function removeRequest(Request $request)
    {
        $this->requests->removeElement($request);
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRequests()
    {
        return $this->requests;
    }

    public function getRequestByArticle() {
        $requests = $this->sentRequests;
        $requestBy = array();

        foreach ($requests as $request) {

            if ($request->getType() == Request::TYPE_REQUEST_NEW or $request->getType() == Request::TYPE_REQUEST_MODIFY) {
                if ($request->getArticle() == null) $key = Request::TYPE_REQUEST_NEW;
                else $key = $request->getArticle()->getId();

                if (!array_key_exists($key, $requestBy)) $requestBy[$key] = new ArrayCollection();
                $requestBy[$key]->add($request);
            }
        }

        return $requestBy;
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPendingRequests()
    {
        $requests = new ArrayCollection();

        foreach($this->requests as $request) {
            if (!$request->isFinished()) $requests->add($request);
        }

        return $requests;
    }



    /**
     * Add receivedMessage.

     *
     * @param \Uzink\BackendBundle\Entity\Message $receivedMessage
     *
     * @return User
     */
    public function addReceivedMessage(Message $receivedMessage)
    {
        $this->receivedMessages[] = $receivedMessage;

        return $this;
    }

    /**
     * Remove receivedMessage.

     *
     * @param \Uzink\BackendBundle\Entity\Message $receivedMessage
     */
    public function removeReceivedMessage(Message $receivedMessage)
    {
        $this->receivedMessages->removeElement($receivedMessage);
    }

    /**
     * Get receivedMessages.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivedMessages()
    {
        return $this->receivedMessages;
    }

    /**
     * Get unreadMessages.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUnreadMessages()
    {
        $unreadMessages = new ArrayCollection();

        foreach ($this->receivedMessages as $currentMessage) {
            if (!$currentMessage->isReaded()) $unreadMessages->add($currentMessage);
        }

        return $unreadMessages;
    }

    /**
     * Add sentMessage.

     *
     * @param \Uzink\BackendBundle\Entity\Message $sentMessage
     *
     * @return User
     */
    public function addSentMessage(Message $sentMessage)
    {
        $this->sentMessages[] = $sentMessage;

        return $this;
    }

    /**
     * Remove sentMessage.

     *
     * @param \Uzink\BackendBundle\Entity\Message $sentMessage
     */
    public function removeSentMessage(Message $sentMessage)
    {
        $this->sentMessages->removeElement($sentMessage);
    }

    /**
     * Get sentMessages.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSentMessages()
    {
        return $this->sentMessages;
    }

    /**
     * Add collaborator.

     *
     * @param \Uzink\BackendBundle\Entity\Article $collaboration
     *
     * @return User
     */
    public function addCollaboration(Article $collaboration)
    {
        if (!in_array($collaboration, $this->collaborations->toArray())) $this->collaborations[] = $collaboration;

        return $this;
    }

    /**
     * Remove collaborator.

     *
     * @param \Uzink\BackendBundle\Entity\Article $collaboration
     */
    public function removeCollaboration(Article $collaboration)
    {
        $this->collaborations->removeElement($collaboration);
    }

    /**
     * Get collaborators.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollaborations()
    {
        return $this->collaborations;
    }

    /**
     * Add activity.

     *
     * @param \Uzink\BackendBundle\Entity\Activity $activity
     *
     * @return User
     */
    public function addActivity(Activity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity.

     *
     * @param \Uzink\BackendBundle\Entity\Activity $activity
     */
    public function removeActivity(Activity $activity)
    {
        $this->activities->removeElement($activity);
    }

    /**
     * Get activities.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }

    //<editor-fold desc="Social Network">
    /**
     * Set socialProfileLink
     *
     * @param string $type
     * @param string $link
     *
     * @return User
     */
    public function setSocialProfileLink($type, $link)     {
        $this->socialProfiles[$type] = $link;

        return $this;
    }

    /**
     * Set socialProfiles
     *
     * @param array $socialProfiles
     *
     * @return User
     */
    public function setSocialProfiles($socialProfiles)     {
        $this->socialProfiles = $socialProfiles;


        return $this;
    }

    /**
     * Get socialProfiles
     *
     * @return array
     */
    public function getSocialProfiles()     {
        return $this->socialProfiles;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set twitterId
     *
     * @param string $twitterId
     *
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitter_id = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitter_id;
    }

    /**
     * Set twitterAccessToken
     *
     * @param string $twitterAccessToken
     *
     * @return User
     */
    public function setTwitterAccessToken($twitterAccessToken)
    {
        $this->twitter_access_token = $twitterAccessToken;

        return $this;
    }

    /**
     * Get twitterAccessToken
     *
     * @return string
     */
    public function getTwitterAccessToken()
    {
        return $this->twitter_access_token;
    }
    //</editor-fold>

    //</editor-fold>

    //<editor-fold desc="Role Handling">
    public function getType() {
        $roles = $this->getRoles();
        if (in_array(self::ROLE_SUPER_ADMIN, $roles)) {
            return 'admin';
        } elseif (in_array(self::ROLE_ADMIN, $roles)) {
            return 'admin';

        } elseif (in_array(self::ROLE_LEADER, $roles)) {
            return 'leader';

        } elseif (in_array(self::ROLE_SUPERVISOR, $roles)) {
            return 'supervisor';

        } elseif (in_array(self::ROLE_EDITOR, $roles)) {
            return 'editor';
        } else {
            return 'user';
        }
    }

    public function getRole() {
        $role = null;

        $roles = $this->getRoles();
        if (in_array(self::ROLE_SUPER_ADMIN, $roles)) {
            $role = self::ROLE_ADMIN;

        } elseif (in_array(self::ROLE_ADMIN, $roles)) {
            $role = self::ROLE_ADMIN;

        } elseif (in_array(self::ROLE_LEADER, $roles)) {
            $role = self::ROLE_LEADER;

        } elseif (in_array(self::ROLE_SUPERVISOR, $roles)) {
            $role = self::ROLE_SUPERVISOR;

        } elseif (in_array(self::ROLE_EDITOR, $roles)) {
            $role = self::ROLE_EDITOR;

        } else {
            $role = self::ROLE_USER;
        }

        return $role;
    }

    public function nextRole() {
        $role = $this->getRole();
        return self::getNextRole($role);
    }

    public function previousRole() {
        $role = $this->getRole();
        return self::getPreviousRole($role);
    }

    public function setType($type) {
        $this->setRoles(array());
        $this->addRole($type);
    }

    public function hasRole($role) {
        $roles = $this->getHierarchyRoles($this->getRole());
        foreach($roles as $currentRole) {
            if ($currentRole == $role) return true;
        }

        return false;
    }

    public static function isRole($role) {
        $roles = self::$roleHierachy;
        return array_key_exists($role, $roles);
    }

    public static function getNextRole($role) {
        $roles = self::$roleHierachy;
        $keys = array_keys($roles);
        $keyIndexes = array_flip($keys);

        if (isset($keys[$keyIndexes[$role]+1]))
            return $keys[$keyIndexes[$role]+1];

        return false;
    }

    public static function getPreviousRole($role) {
        $roles = self::$roleHierachy;
        $keys = array_keys($roles);
        $keyIndexes = array_flip($keys);

        if (isset($keys[$keyIndexes[$role]-1]))
            return $keys[$keyIndexes[$role]-1];

        return false;
    }

    public static function getHierarchyRoles($role) {
        $roles = array();

        if (array_key_exists($role, self::$roleHierachy)) {
            $roles[] = $role;
            foreach (self::$roleHierachy[$role] as $currentRole) {
                $roles = array_merge($roles, self::getHierarchyRoles($currentRole));
            }
        }

        return $roles;
    }

    public static function getUpperRoles($role) {
        $roles = array();

        foreach (self::$roleHierachy as $key => $currentRole) {
            $hierarchyRoles = self::getHierarchyRoles($key);
            if (in_array($role, $hierarchyRoles)) $roles[] = $key;
        }

        return $roles;
    }
    //</editor-fold>

    //<editor-fold desc="Team Handling">
    /**
     * Add child.

     *
     * @param \Uzink\BackendBundle\Entity\User $child
     *
     * @return User
     */
    public function addChild(User $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child.

     *
     * @param \Uzink\BackendBundle\Entity\User $child
     */
    public function removeChild(User $child)
    {
        $this->children->removeElement($child);
    }


    /**
     * Set lft.

     *
     * @param integer $lft
     *
     * @return User
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft.

     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl.

     *
     * @param integer $lvl
     *
     * @return User
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl.

     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt.

     *
     * @param integer $rgt
     *
     * @return User
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt.

     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root.

     *
     * @param integer $root
     *
     * @return User
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root.

     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    public function getWorkgroupUsers($user = null) {
        if (!$user) $leaderUser = $this;
        else $leaderUser = $user;

        while(!$leaderUser->hasRole(User::ROLE_ADMIN)) {
            if ($leaderUser->getParent() == null) break;
            $leaderUser = $leaderUser->getParent();
        };

        $workgroupUsers = $this->getChildrenUsers($leaderUser);
        if (is_array($workgroupUsers)) $workgroupUsers = new ArrayCollection($workgroupUsers);
        $workgroupUsers->add($leaderUser);
        $userIndex = $workgroupUsers->indexOf($leaderUser);
        if ($userIndex) $workgroupUsers->remove($userIndex);

        $iterator = $workgroupUsers->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getName() < $b->getName()) ? -1 : 1;
        });
        $orderCollection = new ArrayCollection(iterator_to_array($iterator));

        return $orderCollection;
    }

    public function getChildrenUsers(User $user = null) {
        if (!$user) $user = $this;
        $workgroupUsers = array();

        $workgroupUsers[] = $user;
        $childrenUsers = $user->getChildren();
        foreach($childrenUsers as $child) {
            if ($child->hasRole(self::ROLE_EDITOR)) {
                $workgroupUsers = array_merge($workgroupUsers, $this->getChildrenUsers($child));
            }
        }

        usort($workgroupUsers, array($this,'userSort'));

        return $workgroupUsers;
    }

    public function getOrderedChildren() {
        $children = $this->getChildren()->toArray();
        usort($children, array($this,'userSort'));

        return $children;
    }

    private static function userSort(User $a, User $b) {
        $aName = (string) $a;
        $bName = (string) $b;
        return strcmp(strtolower($aName), strtolower($bName));
    }

    public function getChildrenUsersByRole($user = null) {
        $workgroupUsers = $this->getChildren();

        $workgroupUsersByRole = array();

        foreach ($workgroupUsers as $currentUser) {
            $role = $currentUser->getRole();
            if (!array_key_exists($role, $workgroupUsersByRole)) $workgroupUsersByRole[$role] = array();
            $workgroupUsersByRole[$role][] = $currentUser;
        }

        $sortedUsers = array();
        $sortedRoles = [self::ROLE_LEADER, self::ROLE_SUPERVISOR, self::ROLE_EDITOR, self::ROLE_USER];
        foreach($sortedRoles as $role) {
            if (array_key_exists($role, $workgroupUsersByRole))
                $sortedUsers[$role] = $workgroupUsersByRole[$role];
        }

        return $sortedUsers;
    }

    public function isWorkgroupUser($user) {
        $users = $this->getWorkgroupUsers();

        foreach ($users as $currentUser) {
            if ($user == $currentUser) return true;
        }

        return false;
    }
    //</editor-fold>

    /**
     * Add sentRequest
     *
     * @param \Uzink\BackendBundle\Entity\Request $sentRequest
     *
     * @return User
     */
    public function addSentRequest(\Uzink\BackendBundle\Entity\Request $sentRequest)
    {
        $this->sentRequests[] = $sentRequest;
    
        return $this;
    }

    /**
     * Remove sentRequest
     *
     * @param \Uzink\BackendBundle\Entity\Request $sentRequest
     */
    public function removeSentRequest(\Uzink\BackendBundle\Entity\Request $sentRequest)
    {
        $this->sentRequests->removeElement($sentRequest);
    }

    /**
     * Get sentRequests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSentRequests()
    {
        return $this->sentRequests;
    }

    /**
     * Get the recipients Name (e.g. "Mr. John Doe")
     * @return string
     */
    public function getDisplayName()
    {
        return $this->__toString();
    }

    /**
     * Get the interval for notifications
     * @return integer
     */
    public function getNotificationMode()
    {
        return RecipientInterface::NOTIFICATION_MODE_IMMEDIATELY;
    }

    /**
     * Whether the recipient likes to get the newsletter or not
     * @return boolean
     */
    public function getNewsletter()
    {
        return false;
    }

    /**
     * Get the recipients prefered locale for the emails
     * @return string
     */
    public function getPreferredLocale()
    {
        return 'es';
    }
}
