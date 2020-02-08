<?php

namespace Uzink\BackendBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\Event;
use Uzink\BackendBundle\Entity\User;

class ActivityEvent extends Event
{
    /**
     * @var object
     */
    protected $entity;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var User
     */
    protected $sender;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $receivers;

    public function __construct($entity, $type, $receivers, $sender = null) {
        $this->entity = $entity;
        $this->type = $type;
        $this->sender = $sender;
        $this->receivers = new ArrayCollection();
        if (is_array($receivers)) {
            foreach($receivers as $receiver) {
                $this->receivers->add($receiver);
            }
        } else {
            $this->receivers->add($receivers);
        }

    }

    public function getType()
    {
        return $this->type;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function getReceivers()
    {
        return $this->receivers;
    }
}