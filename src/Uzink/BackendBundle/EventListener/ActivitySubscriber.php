<?php
namespace Uzink\BackendBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Uzink\BackendBundle\Activity\ActivityFactory;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Event\ActivityEvent;

class ActivitySubscriber implements EventSubscriberInterface
{
    private $activityFactory;

    public function __construct(ActivityFactory $activityFactory) {
        $this->activityFactory = $activityFactory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Activity::EVENT_CREATE_ACTIVITIES => array('createActivities'),
        );
    }

    public function createActivities(ActivityEvent $event)
    {
        $type = $event->getType();
        $entity = $event->getEntity();
        $receivers = $event->getReceivers();
        $sender = $event->getSender();

        $this->activityFactory->create($type, $entity, $receivers, $sender);
    }
}

