<?php
namespace Uzink\BackendBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Entity\UserRepository;
use Uzink\BackendBundle\Mailer\Mailer;

class NewUserListener implements EventSubscriberInterface
{
    /** @var  Mailer */
    private $mailer;

    /** @var EntityManager */
    private $entityManager;

    /**
     * NewUserListener constructor.
     * @param Mailer $mailer
     * @param EntityManager $entityManager
     */
    public function __construct(Mailer $mailer, EntityManager $entityManager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }


    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onNewUserSuccess',
        );
    }

    public function onNewUserSuccess(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        /** @var UserRepository $repo */
        $repo = $this->entityManager->getRepository('BackendBundle:User');
        $administrators = $repo->findUsersByRole(User::ROLE_ADMIN);

        $this->mailer->sendNewUser($user, $administrators);
    }
}