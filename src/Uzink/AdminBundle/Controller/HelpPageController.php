<?php

namespace Uzink\AdminBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Uzink\BackendBundle\Entity\HelpPage;

class HelpPageController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'helpPage';
        $this->entityDescription = 'Páginas de ayuda';
        $this->entityFields = array(
            'title' => 'Título',
            'position' => 'Posición'
        );
    }

    /**
     * Lists all entities.
     */
    public function listAction($role, $page)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem($this->entityDescription);

        /** @var EntityRepository $repository */
        $repository = $this->getManager()->getRepository('BackendBundle:HelpPage');
        $qb = $repository->createQueryBuilder('p');
        $literal = $qb->expr()->literal(constant('Uzink\BackendBundle\Entity\HelpPage::'. strtoupper($role)));
        $query = $qb->where($qb->expr()->eq('p.role', $literal));

        $pager = new Pagerfanta(new DoctrineORMAdapter($query));
        $pager->setMaxPerPage($this->container->getParameter('admin.list_max_per_page'));
        try {
            $pager->setCurrentPage($page);
        } catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return $this->render('AdminBundle:HelpPage:index.html.twig', array(
            'pager' => $pager,
            'metadata' => $this->getMetadata(),
            'role' => $role
        ));
    }

    /**
     * Displays a form to create a new Banner entity.
     */
    public function newAction()
    {
        $role = $this->container->get('request')->get('role');
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $route = $this->get('router')->generate("admin.$this->entityName.index", array('role' => $role));
        $breadcrumbs->addItem($this->entityDescription, $route);
        $breadcrumbs->addItem('Nuevo');

        $entity = $this->getManager()->create();
        $form = $this->getForm($entity);

        $form->get('role')->setData(constant('Uzink\BackendBundle\Entity\HelpPage::'. strtoupper($role)));

        return $this->render('AdminBundle:HelpPage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'metadata' => $this->getMetadata(),
            'role' => $role
        ));
    }

    /**
     * Creates a new entity.
     */
    public function createAction(Request $request)
    {
        $role = $this->container->get('request')->get('role');
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $route = $this->get('router')->generate("admin.$this->entityName.index", array('role' => $role));
        $breadcrumbs->addItem($this->entityDescription, $route);
        $breadcrumbs->addItem('Nuevo');

        $entity = $this->getManager()->create();
        $form = $this->getForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (!$entity->getPosition()) {
                $repository = $this->getDoctrine()->getRepository('Uzink\BackendBundle\Entity\HelpPage');
                $pages = $repository->findByRole(constant('Uzink\BackendBundle\Entity\HelpPage::'. strtoupper($role)));

                $position = 1;
                foreach ($pages as $page) {
                    if ($page->getPosition() >= $position) $position = $page->getPosition() + 1;
                }
                $entity->setPosition($position);
            }

            $entity = $this->getManager()->save($entity);
            $this->get('session')->getFlashBag()->add(
                'success',
                'Elemento creado correctamente'
            );
            return $this->redirect($this->get("router")->generate("admin.$this->entityName.edit", array('id' => $entity->getId())));
        }
        else
        {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Ha ocurrido un error creando el elemento'
            );
        }

        return $this->render('AdminBundle:Default:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'metadata' => $this->getMetadata()
        ));
    }

    /**
     * Displays a form to edit an existing entity.
     */
    public function editAction($id)
    {
        $entity = $this->getManager()->find($id);
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $role = $this->getConstantName('Uzink\BackendBundle\Entity\HelpPage', $entity->getRole());
        $route = $this->get('router')->generate("admin.$this->entityName.index", array('role' => $role));
        $breadcrumbs->addItem($this->entityDescription, $route);
        $breadcrumbs->addItem('Editar');

        if (!$entity) {
            throw $this->createNotFoundException('No se ha encontrado el elemento');
        }

        $form = $this->getForm($entity);

        return $this->render('AdminBundle:HelpPage:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'metadata' => $this->getMetadata(),
            'role' => $role
        ));
    }

    /**
     * Edits an existing entity.
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getManager()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se ha encontrado el elemento');
        }

        $form = $this->getForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getManager()->update($entity);
            $this->get('session')->getFlashBag()->add(
                'success',
                'Elemento actualizado correctamente'
            );
            return $this->redirect($this->generateUrl("admin.$this->entityName.edit", array('id' => $id)));
        }
        else
        {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Ha ocurrido un error modificando el elemento'
            );
        }

        return $this->render('AdminBundle:Default:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'metadata' => $this->getMetadata()
        ));
    }

    private function getConstantName($class, $value) {
        $fooClass = new ReflectionClass ( $class );
        $constants = $fooClass->getConstants();

        $constName = null;
        foreach ( $constants as $name => $constantValue )
        {
            if ( $constantValue == $value )
            {
                $constName = $name;
                break;
            }
        }

        return strtolower($constName);
    }
}
