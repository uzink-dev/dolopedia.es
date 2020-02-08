<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Message
 *
 * @ORM\Table("message")
 * @ORM\Entity(repositoryClass="Uzink\BackendBundle\Entity\MessageRepository"))
 * @Vich\Uploadable
 */
class Message
{
    const RECEIVED = 'received';
    const SENT = 'sent';

    use ORMBehaviors\Timestampable\Timestampable;
    use ORMBehaviors\Blameable\Blameable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var /Uzink/BackendBundle/Entity/User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sentMessages")
     */
    private $sender;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="receivedMessages")
     */
    private $receiver;

    /**
     * @var /Uzink/BackendBundle/Entity/User
     *
     * @ORM\ManyToMany(targetEntity="User")
     */
    private $multipleReceivers;

    /**
     * @var array
     *
     * @ORM\Column(name="multiple_send_metadata", type="json_array")
     */
    private $multipleSendMetadata = array();

    /**
     * @var boolean
     *
     * @ORM\Column(name="multiple_complete", type="boolean", options={"default" : false})
     */
    private $multipleComplete = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="readed", type="boolean")
     */
    private $readed = false;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=500)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text")
     */
    private $type = 'default';

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
     * @ORM\Column(type="boolean", options={"default" : false})
     *
     * @var boolean
     */
    private $multiple = false;

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
     * Set sender.
    
     *
     * @param /Uzink/BackendBundle/Entity/User $sender
     *
     * @return Message
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    
        return $this;
    }

    /**
     * Get sender.
    
     *
     * @return /Uzink/BackendBundle/Entity/User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set receiver.
    
     *
     * @param /Uzink/BackendBundle/Entity/User $receiver
     *
     * @return Message
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    
        return $this;
    }

    /**
     * Get receiver.
    
     *
     * @return /Uzink/BackendBundle/Entity/User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return mixed
     */
    public function getMultipleReceivers()
    {
        return $this->multipleReceivers;
    }

    /**
     * @param mixed $multipleReceivers
     * @return Message
     */
    public function setMultipleReceivers($multipleReceivers)
    {
        $this->multipleReceivers = $multipleReceivers;
        return $this;
    }

    /**
     * @return array
     */
    public function getMultipleSendMetadata()
    {
        return $this->multipleSendMetadata;
    }

    /**
     * @param array $multipleSendMetadata
     * @return Message
     */
    public function setMultipleSendMetadata($multipleSendMetadata)
    {
        $this->multipleSendMetadata = $multipleSendMetadata;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultipleComplete()
    {
        return $this->multipleComplete;
    }

    /**
     * @param bool $multipleComplete
     * @return Message
     */
    public function setMultipleComplete($multipleComplete)
    {
        $this->multipleComplete = $multipleComplete;
        return $this;
    }

    /**
     * Set readed.
    
     *
     * @param boolean $readed
     *
     * @return Message
     */
    public function setReaded($readed)
    {
        $this->readed = $readed;
    
        return $this;
    }

    /**
     * Get readed.
    
     *
     * @return boolean
     */
    public function getReaded()
    {
        return $this->readed;
    }


    public function isReaded()
    {
        if ($this->getReaded()) return true;
        else return false;
    }

    /**
     * Set subject.
    
     *
     * @param string $subject
     *
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject.
    
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body.
    
     *
     * @param string $body
     *
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body.
    
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set type.
    
     *
     * @param string $type
     *
     * @return Message
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
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param boolean $multiple
     * @return Message
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }
}
