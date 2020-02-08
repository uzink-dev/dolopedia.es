<?php

namespace Uzink\ConfiguracionBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Uzink\ConfiguracionBundle\Entity\ParametroConfiguracion;
use Pagerfanta\Pagerfanta,
    Pagerfanta\Adapter\DoctrineORMAdapter,
    Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ParametroConfiguracion manager.
 *
 */
class ParametroConfiguracionManager
{

    /**
     * Holds the Symfony2 event dispatcher service
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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, $request )
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $this->request = $request;
    }


    /**
     * Return pager for ParametroConfiguracion
     * 
     * @param type $page
     * @param type $maxPerPage
     * @return \Pagerfanta
     * @throws NotFoundHttpException
     */
    public function getPager($page, $maxPerPage)
    {
        $query = $this->repo->createQueryBuilder('p')->getQuery();
        $pager = new Pagerfanta(new DoctrineORMAdapter($query));
		$pager->setMaxPerPage($maxPerPage);
        try {
			$pager->setCurrentPage($page);
		} catch(NotValidCurrentPageException $e) {
			throw new NotFoundHttpException();
		}
        return $pager;
    }

    /**
     * Finds ParametroConfiguracion
     * @param type $id
     * @return ParametroConfiguracion
     */
    public function findParametroConfiguracion($id)
    {
        return $this->repo->find($id);
    }
    
    /**
     * @return ParametroConfiguracion
     */
    public function createParametroConfiguracion()
    {
        $class = $this->class;
        $parametroConfiguracion = new $class();
        return $parametroConfiguracion;
    }
    
    /**
     * 
     * 
     * @param Uzink\ConfiguracionBundle\Entity\ParametroConfiguracion $parametroConfiguracion
     */
    public function saveParametroConfiguracion(ParametroConfiguracion $parametroConfiguracion)
    {
        $this->em->persist($parametroConfiguracion);
        $this->em->flush();
        return $parametroConfiguracion;
    }
    
    /**
     * 
     * @param \Uzink\ParametroConfiguracionBundle\Entity\ParametroConfiguracion $parametroConfiguracion
     */
    public function updateParametroConfiguracion(ParametroConfiguracion $parametroConfiguracion)
    {
        $this->em->persist($parametroConfiguracion);
        $this->em->flush();
        return $parametroConfiguracion;
    }
    
    public function deleteParametroConfiguracion(ParametroConfiguracion $parametroConfiguracion)
    {
        $this->em->remove($parametroConfiguracion);
        $this->em->flush();
    }

}
