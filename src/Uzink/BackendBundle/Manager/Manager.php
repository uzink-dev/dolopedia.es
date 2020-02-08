<?php
namespace Uzink\BackendBundle\Manager;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\ArticleRepository;
use Uzink\BackendBundle\Entity\Message;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Event\ActivityEvent;

class Manager extends ContainerAware {
    //<editor-fold desc="Properties & Basic">
    const ITEMS_PER_PAGE = 10;

    /**
     * @var AclManager
     */
    protected $aclManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $formClass;

    /**
     * @var mixed
     */
    protected $handler;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var ArticleRepository
     */
    protected $repo;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, $handler = null)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->handler = $handler;
        $this->repo = $em->getRepository($class);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->repo = $this->em->getRepository($this->class);
        $this->dispatcher = $this->container->get('event_dispatcher');
        $this->aclManager = $this->container->get('uzink.acl.manager');
    }

    //</editor-fold>

    //<editor-fold desc="Manager Basic">
    public function getRepository()
    {
        return $this->repo;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function getPager($entities, $itemsPerPage = null, $page = 1) {
        if (!$itemsPerPage) {
            $rf = new \ReflectionClass($this->class);
            $classItemsPerPage = $rf->getConstant('ITEMS_PER_PAGE');

            if (false === $classItemsPerPage or !is_numeric($classItemsPerPage))
                $itemsPerPage = self::ITEMS_PER_PAGE;
            else
                $itemsPerPage = $classItemsPerPage;
        }

        $pagerAdapter = new ArrayAdapter($entities);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setMaxPerPage($itemsPerPage);
        if ($page) $pager->setCurrentPage($page);

        return $pager;
    }

    //</editor-fold>

    //<editor-fold desc="Entity Handling Basic">
    public function get($id)
    {
        if (!$id) return null;

        $entity = $this->repo->find($id);
        if (!$entity) throw new NotFoundHttpException('Resource Not Found');
        return $entity;
    }

    public function create()
    {
        $class = $this->class;
        $entity = new $class();
        return $entity;
    }

    /**
     * @param Message $entity
     * @return Message mixed
     */
    public function save(&$entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        if ($entity instanceof Message && !$entity->getMultiple() && !$entity->getReaded()) {
            $receivers = [$entity->getSender(), $entity->getReceiver()];
            $event = new ActivityEvent($entity, Activity::TYPE_USER_NEW_MESSAGE, $receivers, $entity->getSender());
            $this->dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

            $mailer = $this->container->get('uzink.mailer');
            $mailer->sendNewMessage($entity->getReceiver(), $entity);
        }

        return $entity;
    }

    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    public function delete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function getForm()
    {
        if ($this->formClass) {
            return new $this->formClass();
        }
    }
    //</editor-fold>

    //<editor-fold desc="Service Setters">
    /**
     * @param AclManager $aclManager
     */
    public function setAclManager(AclManager $aclManager)
    {
        $this->aclManager = $aclManager;
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
        $this->repo = $this->em->getRepository($this->class);
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public function setRequestStack(RequestStack $requestStack) {

    }

    //</editor-fold>
}