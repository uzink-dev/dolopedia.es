<?php
namespace Uzink\BackendBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Category;
use Uzink\BackendBundle\Entity\Draft;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Event\ActivityEvent;
use Uzink\BackendBundle\Event\ActivityRequestEvent;
use Uzink\BackendBundle\Security\Permission\PermissionMap;

class RequestManager extends Manager
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $this->tokenStorage = $this->container->get('security.token_storage');
    }

    public function save(&$entity) {
        parent::save($entity);

        $eventType = null;

        switch ($entity->getType()) {
            case Request::TYPE_REQUEST_NEW:
                $eventType = Activity::TYPE_ARTICLE_REQUEST_NEW;
                break;
            case Request::TYPE_REQUEST_MODIFY:
                $eventType = Activity::TYPE_ARTICLE_REQUEST_MODIFY;
                break;
            case Request::TYPE_EDITION_CREATION:
                $eventType = Activity::TYPE_ARTICLE_EDITION_CREATION;
                break;
            case Request::TYPE_EDITION_MODIFICATION:
                $eventType = Activity::TYPE_ARTICLE_EDITION_MODIFICATION;
                break;
        }

        if ($eventType) {
            $receivers = array();
            $receivers[] = $entity->getUserFrom();
            $receivers[] = $entity->getUserTo();

            $event = new ActivityEvent($entity, $eventType, $receivers, $entity->getUserFrom());
            $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

            $mailer = $this->container->get('uzink.mailer');
            $mailer->sendRequestEmail($entity->getUserTo(), $entity);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @internal param string $type
     * @internal param null|Article|Category|Request $type
     * @return Request
     */
    public function create() {
        $args = func_get_args();
        $type = isset($args[0]) ? func_get_arg(0) : null;
        $entity = isset($args[1]) ? func_get_arg(1) : null;
        if (!$type) throw new InvalidArgumentException('This method require at least the type parameter');

        $translator = $this->container->get('translator');
        $user = $this->tokenStorage->getToken()->getUser();

        $request = new Request();
        switch ($type) {
            case Request::TYPE_REQUEST_NEW:
                if ($entity && $entity instanceof Category) {
                    $request->setType(Request::TYPE_REQUEST_NEW);
                    $request->setCategory($entity);
                    $request->setUserFrom($user);
                    $request->setUserTo($entity->getOwner());
                    break;
                }

                throw new InvalidArgumentException('This request creation (' . Request::TYPE_REQUEST_NEW . ') require a category to initialize values');
                break;

            case Request::TYPE_REQUEST_MODIFY:
                if ($entity && $entity instanceof Article) {
                    $request->setType(Request::TYPE_EDITION_CREATION);
                    $request->setArticle($entity);
                    $request->setCategory($entity->getCategory());
                    $request->setUserFrom($user);
                    $userTo = $entity->getSupervisor() == null ? $entity->getSupervisor() : $entity->getCategory()->getOwner();
                    $request->setUserTo($userTo);
                    break;
                }

                throw new InvalidArgumentException('This request creation (' . Request::TYPE_REQUEST_MODIFY . ') require an category to initialize values');
                break;

            case Request::TYPE_EDITION_CREATION:
                if ($entity && $entity instanceof Request) {
                    $request->setType(Request::TYPE_EDITION_CREATION);
                    $request->setTitle($entity->getTitle());
                    $request->setCategory($entity->getCategory());
                    $request->setContent($entity->getContent());
                    $request->setUserFrom($entity->getUserTo());
                    $request->setUserTo($entity->getAssignedUser());
                    $request->setPreviousRequest($entity);

                    break;
                }

                throw new InvalidArgumentException('This request creation (' . Request::TYPE_EDITION_CREATION . ') require a request to initialize values');
                break;

            case Request::TYPE_EDITION_MODIFICATION;
                if ($entity && $entity instanceof Request && $entity->getArticle()) {
                    $article = $entity->getArticle();

                    $request->setType(Request::TYPE_EDITION_MODIFICATION);
                    $request->setTitle($article->getTitle());
                    $request->setArticle($article);
                    $request->setCategory($article->getCategory());
                    $request->setContent($entity->getContent());
                    $request->setUserFrom($entity->getUserTo());
                    $request->setUserTo($article->getEditor());
                    $request->setPreviousRequest($entity);

                    break;
                }

                throw new InvalidArgumentException('This request creation (' . Request::TYPE_EDITION_MODIFICATION . ') require a request with an article to initialize values');

                break;
            case Request::TYPE_ARTICLE_VALIDATION;
                if ($entity && $entity instanceof Draft) {
                    $article = $entity->getArticle();

                    $request->setType(Request::TYPE_ARTICLE_VALIDATION);
                    $request->setDraft($entity);
                    $request->setArticle($article);
                    $request->setTitle($entity->getTitle());
                    $request->setCategory($entity->getCategory());
                    $requestContent = $translator->trans('request.revisionDescription', array(), 'dolopedia');
                    $request->setContent($requestContent);
                    $request->setUserFrom($article->getEditor());
                    $request->setUserTo($article->getSupervisor());

                    break;
                }

                throw new InvalidArgumentException('This request creation (' . Request::TYPE_ARTICLE_VALIDATION . ') require a draft to initialize values');

                break;
            case Request::TYPE_ARTICLE_PUBLICATION;
                if ($entity && $entity instanceof Draft) {
                    $article = $entity->getArticle();

                    $request->setType(Request::TYPE_ARTICLE_PUBLICATION);
                    $request->setDraft($entity);
                    $request->setArticle($article);
                    $request->setTitle($entity->getTitle());
                    $request->setCategory($entity->getCategory());
                    $requestContent = $translator->trans('request.publishDescription', array(), 'dolopedia');
                    $request->setContent($requestContent);
                    $request->setUserFrom($article->getSupervisor());
                    $request->setUserTo($article->getOwner());

                    break;
                }

                throw new InvalidArgumentException('This request creation (' . Request::TYPE_ARTICLE_PUBLICATION . ') require a draft to initialize values');

                break;
        }

        return $request;
    }

    /**
     * @param Request $entity
     * @return null|void
     */
    public function setPermissions($entity)
    {
        $aclManager = $this->aclManager;

        $users = array();
        $users['to'] = $entity->getUserTo();
        $users['from'] = $entity->getUserFrom();

        $aclManager->clean($entity, $users);

        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), User::ROLE_ADMIN);
        $aclManager->grant($entity, array(PermissionMap::PERMISSION_VIEW), $users['from']);
        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), $users['to']);
    }
}