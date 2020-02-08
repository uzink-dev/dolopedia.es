<?php

namespace Uzink\UtilsBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MailingEvent extends Event
{

    protected $receptor;
    protected $entity;

    public function __construct($receptor, $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }
    
    public function getReceptor()
    {
        return $this->receptor;
    }    
}