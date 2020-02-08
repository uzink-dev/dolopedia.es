<?php
namespace Uzink\BackendBundle\Workflow\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\WorkflowBundle\Event\StepEvent;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Event\ActivityEvent;
use Uzink\BackendBundle\Handler\WorkflowHandler;
use Uzink\BackendBundle\Manager\ArticleManager;
use Uzink\BackendBundle\Manager\RequestManager;

class ArticleEditionProcessSubscriber extends ContainerAware implements EventSubscriberInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Uzink\BackendBundle\Manager\ArticleManager
     */
    private $articleManager;

    /**
     * @var \Uzink\BackendBundle\Manager\RequestManager
     */
    private $requestManager;

    /**
     * @var \Uzink\BackendBundle\Handler\WorkflowHandler
     */
    private $wfHandler;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private $tokenStorage;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->wfHandler = $this->container->get('uzink.workflow.handler');
        $this->requestManager = $this->container->get('uzink.request.manager');
        $this->articleManager = $this->container->get('uzink.article.manager');
        $this->dispatcher = $this->container->get('event_dispatcher');
        $this->tokenStorage = $this->container->get('security.token_storage');
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
    }


    public static function getSubscribedEvents()
    {
        // Steps Reached
        return array(
            'article_edition.revision.reached' => array(
                'handleReachedRevision',
            ),
            'article_edition.validated.reached' => array(
                'handleReachedValidated',
            ),
            'article_edition.published.reached' => array(
            'handleReachedPublished',
            )            
        );
    }

    // Steps Handling
    public function handleReachedRevision(StepEvent $event)
    {
        $draft = $event->getModel()->getEntity();
        $validationRequest = $this->requestManager->create(Request::TYPE_ARTICLE_VALIDATION, $draft);

        $this->requestManager->save($validationRequest);
        $this->wfHandler->startAndSave($validationRequest);

        $user = $this->tokenStorage->getToken()->getUser();

        $receivers = array();
        $receivers[] = $user;
        if ($user != $validationRequest->getUserTo()) $receivers[] = $validationRequest->getUserTo();

        $event = new ActivityEvent($validationRequest, Activity::TYPE_ARTICLE_EDITION_REVISION, $receivers, $user);
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

        $mailer = $this->container->get('uzink.mailer');
        $mailer->sendValidationEmail($validationRequest->getUserTo(), $draft->getArticle());
    }

    public function handleReachedValidated(StepEvent $event)
    {
        $draft = $event->getModel()->getEntity();
        $publicationRequest = $this->requestManager->create(Request::TYPE_ARTICLE_PUBLICATION, $draft);

        $this->requestManager->save($publicationRequest);
        $this->wfHandler->startAndSave($publicationRequest);

        $user = $this->tokenStorage->getToken()->getUser();
        $receivers = array();
        $receivers[] = $user;
        $article = $draft->getArticle();
        if ($article->getEditor()) $receivers[] = $article->getEditor();
        $receivers[] = $article->getSupervisor();

        $event = new ActivityEvent($draft, Activity::TYPE_ARTICLE_EDITION_VALIDATED, $receivers, $user);
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

        $receivers[] = $article->getOwner();
        $receivers[] = $publicationRequest->getUserTo();

        $event = new ActivityEvent($publicationRequest, Activity::TYPE_ARTICLE_EDITION_PUBLICATION, $receivers, $user);
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

        $mailer = $this->container->get('uzink.mailer');
        $mailer->sendValidationEmail($publicationRequest->getUserTo(), $draft->getArticle());
    }

    public function handleReachedPublished(StepEvent $event)
    {
        $draft = $event->getModel()->getEntity();
        $article = $draft->getArticle();

        $article->fillFromDraft($draft);
        $article->setPublished(true);

        $this->articleManager->save($article);

        $user = $this->tokenStorage->getToken()->getUser();
        $receivers = array();
        $receivers[] = $user;
        if ($article->getEditor()) $receivers[] = $article->getEditor();
        $receivers[] = $article->getSupervisor();
        $receivers[] = $article->getOwner();

        $event = new ActivityEvent($article, Activity::TYPE_ARTICLE_EDITION_PUBLISHED, $receivers, $user);
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

        $mailer = $this->container->get('uzink.mailer');
        $sended = new ArrayCollection();
        foreach ($receivers as $receiver) {
            if (!$sended->contains($receiver)) {
                $mailer->sendPublicationEmail($receiver, $draft->getArticle());
                $sended->add($receiver);
            }
        }

        $userRepo = $this->entityManager->getRepository('BackendBundle:User');

        // Users who marked the article like favourite
        $receivers = $userRepo->findUsersWithFavourite($article);
        $event = new ActivityEvent($article, Activity::TYPE_ARTICLE_PUBLISHED, $receivers);
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);


    }
}

