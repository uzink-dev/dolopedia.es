<?php
namespace Uzink\BackendBundle\Workflow\Listener;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\WorkflowBundle\Event\StepEvent;
use Lexik\Bundle\WorkflowBundle\Event\ValidateStepEvent;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Event\ActivityEvent;
use Uzink\BackendBundle\Event\ActivityRequestDecisionEvent;
use Uzink\BackendBundle\Event\ActivityRequestEvent;
use Uzink\BackendBundle\Handler\WorkflowHandler;
use Uzink\BackendBundle\Manager\RequestManager;

class ArticleRequestProcessSubscriber extends ContainerAware implements EventSubscriberInterface
{
    /**
     * @var \Uzink\BackendBundle\Handler\WorkflowHandler
     */
    private $wfHandler;

    /**
     * @var \Uzink\BackendBundle\Manager\RequestManager
     */
    private $requestManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var
     */
    private $dispatcher;

    public function __construct(WorkflowHandler $wfHandler, EntityManager $em, RequestManager $requestManager, EventDispatcherInterface $dispatcher) {
        $this->wfHandler = $wfHandler;
        $this->requestManager = $requestManager;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents()
    {
        return array(
            // Validations
            'article_request.accepted.validate' => array(
                'handleValidationAccepted',
            ),
            'article_request.declined.validate' => array(
                'handleValidationDeclined',
            ),

            // Steps Handling
            'article_request.accepted.reached' => array(
                'handleReachedAccepted',
            ),
            'article_request.declined.reached' => array(
                'handleReachedDeclined',
            ),
        );
    }

    // Validations
    public function handleValidationAccepted(ValidateStepEvent $event)
    {
        $request = $event->getModel()->getEntity();

        switch ($request->getType()) {
            case Request::TYPE_REQUEST_NEW:
                break;
            case Request::TYPE_REQUEST_MODIFY:
                $article = $request->getArticle();
                if (!$article) $event->addViolation('An modify request must have an assigned article');
                break;
            case Request::TYPE_ARTICLE_VALIDATION:
                break;
            case Request::TYPE_ARTICLE_PUBLICATION:
                break;
        }
    }

    public function handleValidationDeclined(ValidateStepEvent $event)
    {
        $request = $event->getModel()->getEntity();

        switch ($request->getType()) {
            case Request::TYPE_REQUEST_NEW:
            case Request::TYPE_REQUEST_MODIFY:
                if (!$request->getReasonToDecline()) $event->addViolation('request.validationError.requestMustHaveAReasonToDecline');
                break;
            case Request::TYPE_ARTICLE_VALIDATION:
                break;
            case Request::TYPE_ARTICLE_PUBLICATION:
                break;
        }
        $model = $event->getModel();
        if ( $model->getEntity()->getReasonToDecline() == null) {
            $event->addViolation('A declined request must have a reason to decline');
        }
    }

    // Steps Handling
    public function handleReachedAccepted(StepEvent $event)
    {
        // Start a new article creation workflow thread
        $request = $event->getModel()->getEntity();

        switch ($request->getType()) {
            case Request::TYPE_REQUEST_NEW:
                $newRequest = $this->requestManager->create(Request::TYPE_EDITION_CREATION, $request);

                $receivers = array();
                $receivers[] = $request->getUserFrom();
                $receivers[] = $request->getUserTo();
                $event = new ActivityEvent($request, Activity::TYPE_REQUEST_ACCEPT, $receivers, $request->getUserTo());
                $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

                $mailer = $this->container->get('uzink.mailer');
                $mailer->sendRequestResponseEmail($request->getUserFrom(), $request);

                break;
            case Request::TYPE_REQUEST_MODIFY:
                if ($request->getAssignedUser()) {
                    $article = $request->getArticle();
                    $userFrom = $request->getUserFrom();

                    $article->setEditor($request->getAssignedUser());
                    $article->addCollaborator($userFrom);

                    $this->em->persist($article);
                    $this->em->flush();
                }

                $newRequest = $this->requestManager->create(Request::TYPE_EDITION_MODIFICATION, $request);

                $receivers = array();
                $receivers[] = $request->getUserFrom();
                $receivers[] = $request->getUserTo();
                $event = new ActivityEvent($request, Activity::TYPE_REQUEST_ACCEPT, $receivers, $request->getUserTo());
                $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

                $mailer = $this->container->get('uzink.mailer');
                $mailer->sendRequestResponseEmail($request->getUserFrom(), $request);

                break;
            case Request::TYPE_ARTICLE_VALIDATION:
                break;
            case Request::TYPE_ARTICLE_PUBLICATION:
                break;
        }

        if (isset($newRequest)) {
            $this->requestManager->save($newRequest);
            $this->wfHandler->startAndSave($newRequest);
        }
    }

    public function handleReachedDeclined(StepEvent $event)
    {
        $request = $event->getModel()->getEntity();

        $receivers = array();
        $receivers[] = $request->getUserFrom();
        $receivers[] = $request->getUserTo();
        $event = new ActivityEvent($request, Activity::TYPE_REQUEST_DECLINE, $receivers, $request->getUserTo());
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

        $mailer = $this->container->get('uzink.mailer');
        $mailer->sendRequestResponseEmail($request->getUserFrom(), $request);
    }
}

