<?php

namespace Uzink\BackendBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Category;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Event\ActivityArticleEvent;
use Uzink\BackendBundle\Event\ActivityEvent;

class ChangeWatcherSubscriber extends ContainerAware implements EventSubscriber
{
    /**
     * @var \Uzink\BackendBundle\Manager\ArticleManager
     */
    private $manager;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->dispatcher = $this->container->get('event_dispatcher');
        $this->tokenStorage = $this->container->get('security.token_storage');
    }

    public function getSubscribedEvents()
    {
        return [
            'preUpdate',
            'postPersist',
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->updatePermissions($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->updatePermissions($entity);
        $this->eventLauncher($args);
    }

    private function eventLauncher(PreUpdateEventArgs $args)
    {
        $eventManager = $args->getEntityManager()->getEventManager();
        $eventManager->removeEventListener('preUpdate', $this);

        $entity = $args->getObject();
        $token = $this->tokenStorage->getToken();
        if (!$token) return;

        $user = $this->tokenStorage->getToken()->getUser();
        if ($entity instanceof Article) {
            switch (true) {
                case $args->hasChangedField('editor'):
                    if ($args->getNewValue('editor')) {
                        $event = new ActivityEvent($entity, Activity::TYPE_ARTICLE_NEW_EDITOR, $args->getNewValue('editor'), $user);
                        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

                        $mailer = $this->container->get('uzink.mailer');
                        $mailer->sendNewAssigmentEmail($entity->getEditor(), $entity);
                    }

                    break;

                case $args->hasChangedField('supervisor'):
                    if ($args->getNewValue('supervisor')) {
                        $event = new ActivityEvent($entity, Activity::TYPE_ARTICLE_NEW_SUPERVISOR, $args->getNewValue('supervisor'), $user);
                        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

                        $mailer = $this->container->get('uzink.mailer');
                        $mailer->sendNewAssigmentEmail($entity->getSupervisor(), $entity);
                    }

                    break;
            }
        } elseif ($entity instanceof Category) {
            switch (true) {
                case $args->hasChangedField('owner'):
                    $event = new ActivityEvent($entity, Activity::TYPE_CATEGORY_NEW_OWNER, $args->getNewValue('owner'), $user);

                    $articles = $entity->getArticles();
                    /** @var Article $article */
                    foreach($articles as $article) {
                        $article->setSupervisor($args->getNewValue('owner'));
                        $args->getEntityManager()->persist($article);
                    }

                    $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);
                    break;
            }
        }
    }

    private function updatePermissions($entity) {
        switch (true) {
            case ($entity instanceof Article):
                $this->manager = $this->container->get('uzink.article.manager');
                $this->manager->setPermissions($entity);

                break;

            case ($entity instanceof Category):
                $this->manager = $this->container->get('uzink.category.manager');
                $this->manager->setPermissions($entity);

                break;

            case ($entity instanceof Request):
                $this->manager = $this->container->get('uzink.request.manager');
                $this->manager->setPermissions($entity);

                break;
        }
    }


}