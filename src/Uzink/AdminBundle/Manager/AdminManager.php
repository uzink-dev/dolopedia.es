<?php

namespace Uzink\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminManager {
    /**
     * Holds the Symfony2 event dispatcher service
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;

    /**
     * Constructor
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository($this->class);
    }

    /**
     * Return pager for Banner
     *
     * @param integer $page
     * @param integer $maxPerPage
     * @return Pagerfanta
     * @throws NotFoundHttpException
     */
    public function getPager($page, $maxPerPage)
    {
        if ($this->class == 'Uzink\BackendBundle\Entity\User') {
            $query = $this
                ->repo
                ->createQueryBuilder('p')
                ->addOrderBy('p.surname1', 'ASC')
                ->addOrderBy('p.name', 'ASC')
                ->addOrderBy('p.email', 'ASC')
                ->getQuery();
        } else {
            $query = $this->repo->createQueryBuilder('p')->getQuery();
        }
        $pager = new Pagerfanta(new DoctrineORMAdapter($query));
        $pager->setMaxPerPage($maxPerPage);
        try {
            $pager->setCurrentPage($page);
        } catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }
        return $pager;
    }

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

    /**
     * Finds Banner
     * @param integer $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->repo->find($id);
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return new $this->class;
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
     * @param mixed $entity
     * @return mixed
     */
    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
     * @param mixed $entity
     * @return mixed
     */
    public function delete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }
}