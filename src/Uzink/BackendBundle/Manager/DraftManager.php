<?php
namespace Uzink\BackendBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Draft;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Event\ActivityArticlePublicationEvent;
use Uzink\BackendBundle\Event\ActivityEvent;
use Uzink\BackendBundle\Security\Permission\PermissionMap;

class DraftManager extends Manager
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var ArticleManager
     */
    private $articleManager;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->tokenStorage = $this->container->get('security.token_storage');
        $this->articleManager = $this->container->get('uzink.article.manager');
    }

    public function create($category = null)
    {
        $article = new Article();

        $currentUser = $this->tokenStorage->getToken()->getUser();
        $article->setOwner($currentUser);
        $article->setSupervisor($currentUser);
        $article->setEditor($currentUser);

        if ($category) {
            $article->setCategory($category);
            $article->setSupervisor($category->getOwner());
        }

        return $article;
    }

    public function get($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) $entity = $this->em->getRepository('BackendBundle:Draft')->find($id);
        if (!$entity) throw new NotFoundHttpException('Resource Not Found');
        return $entity;
    }

    public function save(&$entity, $updateCollaborators = false) {
        if ($entity instanceof Draft && $updateCollaborators) {
            $article = $entity->getArticle();

            if($article->getOwner()) $article->addCollaborators($article->getOwner());
            if($article->getSupervisor()) $article->addCollaborators($article->getSupervisor());
            if($article->getEditor()) $article->addCollaborators($article->getEditor());

            parent::save($article);
        }

        parent::save($entity);

        $this->articleManager->refreshBibliographicEntries($entity);
    }

    public function getDraft($id) {
        if ($id instanceof Article) $id = $id->getId();
        $article = $this->get($id);
        $draft = $this->repo->findLastDraft($article);
        if (!$draft) $draft = new Draft($article);

        return $draft;
    }

    public function saveDraft(Draft &$draft) {
        $newDraft = new Draft($draft);
        $this->save($newDraft);

        if ($this->em->contains($draft)) $this->em->refresh($draft);

        $wfHandler = $this->container->get('uzink.workflow.handler');
        $wfHandler->startAndSave($newDraft);
    }

    public function reviseDraft(Draft $draft) {
        $this->save($draft, true);

        $wfHandler = $this->container->get('uzink.workflow.handler');
        $wfHandler->reachNextState($draft, Draft::STEP_REVISE);
    }

    public function validateDraft(Draft $draft) {
        $this->save($draft, true);

        $request = $this->getRequestFromDraft($draft, Request::TYPE_ARTICLE_VALIDATION);

        $wfHandler = $this->container->get('uzink.workflow.handler');
        $wfHandler->reachNextState($draft, Draft::STEP_VALIDATE);
        $wfHandler->reachNextState($request, Request::STEP_ACCEPT);
    }

    public function noValidateDraft(Draft $draft) {
        $this->save($draft, true);

        $request = $this->getRequestFromDraft($draft, Request::TYPE_ARTICLE_VALIDATION);

        $wfHandler = $this->container->get('uzink.workflow.handler');
        $wfHandler->reachNextState($draft, Draft::STEP_DRAFT);
        $wfHandler->reachNextState($request, Request::STEP_DECLINE);

        $user = $this->tokenStorage->getToken()->getUser();

        $article = $draft->getArticle();
        $receivers = array();
        $receivers[] = $user;
        $receivers[] = $article->getEditor();
        $receivers[] = $article->getSupervisor();

        $event = new ActivityEvent($article, Activity::TYPE_ARTICLE_EDITION_NOT_VALIDATED, $receivers, $user);
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);
    }

    public function publishDraft(Draft $draft) {
        $this->save($draft, true);

        $request = $this->getRequestFromDraft($draft, Request::TYPE_ARTICLE_PUBLICATION);

        $wfHandler = $this->container->get('uzink.workflow.handler');
        $wfHandler->reachNextState($draft, Draft::STEP_PUBLISH);
        $wfHandler->reachNextState($request, Request::STEP_ACCEPT);
    }

    public function noPublishDraft(Draft $draft) {
        $this->save($draft, true);

        $request = $this->getRequestFromDraft($draft, Request::TYPE_ARTICLE_PUBLICATION);

        $wfHandler = $this->container->get('uzink.workflow.handler');
        $wfHandler->reachNextState($draft, Draft::STEP_DRAFT);
        $wfHandler->reachNextState($request, Request::STEP_DECLINE);

        $user = $this->tokenStorage->getToken()->getUser();

        $article = $draft->getArticle();
        $receivers = array();
        $receivers[] = $user;
        $receivers[] = $article->getEditor();
        $receivers[] = $article->getSupervisor();
        $receivers[] = $article->getOwner();

        $event = new ActivityEvent($article, Activity::TYPE_ARTICLE_EDITION_NOT_PUBLISHED, $receivers, $user);
        $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);
    }

    public function getByCategory($category) {
        $criteria = array('category' => $category);
        $orderBy = array('position' => 'ASC', 'id' => 'ASC');

        $articles = $this->repo->findBy($criteria, $orderBy);
        return $articles;
    }

     //<editor-fold desc="Permissions Handling">
    /**
     * @param Draft|Article $entity
     * @return null|void
     * @throws \Symfony\Component\Security\Core\Exception\InvalidArgumentException
     */
    public function setPermissions($entity)
    {
        $aclManager = $this->aclManager;

        if ($entity instanceof Draft) $article = $entity->getArticle();
        elseif ($entity instanceof Article) $article = $entity;
        else throw new InvalidArgumentException('The entity must be an Article or a Draft, given ' . get_class($entity));

        $owner = $article->getOwner();
        $supervisor = $article->getSupervisor();
        $editor = $article->getEditor();

        $users = array();
        $users[] = $owner;

        $aclManager->clean($entity, $users);

        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), User::ROLE_ADMIN);
        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), User::ROLE_SUPER_ADMIN);
        if ($owner) $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), $owner);
        if ($supervisor && $supervisor != $owner) {
            $aclManager->grant($entity, array(PermissionMap::PERMISSION_EDIT), $supervisor);
            $users[] = $supervisor;
        }

        if ($editor && !$this->inArray($editor, $users)) {
            $aclManager->grant($entity, array(PermissionMap::PERMISSION_CONTENT), $editor);
        }
    }

    /**
     * @param Draft|Article $entity
     * @param  $users
     * @throws \Symfony\Component\Security\Core\Exception\InvalidArgumentException
     */
    public function updatePermissions($entity, $users)
    {
        if ($entity instanceof Draft) $article = $entity->getArticle();
        elseif ($entity instanceof Article) $article = $entity;
        else throw new InvalidArgumentException('The entity must be an Article or a Draft, given ' . get_class($entity));

        $aclManager = $this->aclManager;

        $owner = null;
        $supervisor = null;
        $editor = null;

        foreach ($users as $key => $user) {
            switch ($key) {
                case 'owner':
                    $owner = $user;
                    break;
                case 'supervisor':
                    $supervisor = $user;
                    break;
                case 'editor':
                    $editor = $user;
                    break;
            }
        }

        $oldUsers = array();

        if ($owner) {
            $aclManager->revoke($article, array('owner'), $owner['old']);
            $oldUsers[] = $owner['old'];
        }

        if ($supervisor) {
            if ($supervisor['old'] && !$this->inArray($supervisor['old'], $oldUsers)) {
                $aclManager->revoke($article, array(PermissionMap::PERMISSION_EDIT), $supervisor['old']);
                $oldUsers[] = $supervisor['old'];
            }
        }

        if ($editor && $editor['new']) {
            if ($editor['old'] && !$this->inArray($editor['old'], $oldUsers)) {
                $aclManager->revoke($article, array(PermissionMap::PERMISSION_EDIT), $editor['old']);
                $oldUsers[] = $editor['old'];
            }
        }

        $this->setPermissions($article);
    }

    private function inArray(User $user, $array) {
        foreach ($array as $currentUser) {
            if ($currentUser == $user) return true;
        }

        return false;
    }
    //</editor-fold>

    private function getRequestFromDraft(Draft $draft, $type) {
        $repository  = $this->em->getRepository('BackendBundle:Request');
        $criteria = array(
            'draft' => $draft,
            'type'  => $type
        );

        $request = $repository->findOneBy($criteria);
        return $request;
    }
}