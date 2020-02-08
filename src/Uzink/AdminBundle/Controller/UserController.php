<?php

namespace Uzink\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Uzink\AdminBundle\Form\DeleteUserType;
use Uzink\AdminBundle\Form\UserFiltersType;
use Uzink\BackendBundle\Entity\UserRepository;

class UserController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'user';
        $this->entityDescription = 'Usuarios';
        $this->entityFields = array(
            'name'              => 'Nombre',
            'surname1'          => 'Apellido',
            'role'              => 'Rol',
            'email'             => 'Email',
            'parent'            => 'Usuario Padre',
            'children'          => 'Usuarios hijo',
            'assignedArticles'  => 'Artículos asignados',
            'createdAt'         => 'Fecha de alta',
            'workplace'         => 'Lugar de trabajo'
        );
    }

    /**
     * Lists all entities.
     */
    public function indexAction($page = 1)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem($this->entityDescription);

        $request = $this->container->get('request');
        $form = $this->createForm(new UserFiltersType(), null, array('method' => 'GET'));
        $form->handleRequest($request);

        /** @var UserRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository('BackendBundle:User');
        $query = $repo->findGridUsers($this->handleFilters($form));
        $pager = new Pagerfanta(new DoctrineORMAdapter($query));
        $pager->setMaxPerPage(50);
        $pager->setCurrentPage($page);

        return $this->render('AdminBundle:User:index.html.twig', array(
            'form' => $form->createView(),
            'pager' => $pager,
            'metadata' => $this->getMetadata()
        ));
    }

    protected function handleFilters(Form $form)
    {
        $filters = array();

        $filters['name'] = $form->get('name')->getData()?:null;
        $filters['surnames'] = $form->get('surnames')->getData()?:null;
        $filters['email'] = $form->get('email')->getData()?:null;
        $filters['createdAtFrom'] = $form->get('createdAtFrom')->getData()?:null;
        $filters['createdAtTo'] = $form->get('createdAtTo')->getData()?:null;
        $filters['role'] = $form->get('role')->getData()?:null;

        return $filters;
    }

    /**
     * Delete
     */
    public function deleteAction(Request $request, $id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem($this->entityDescription, $this->get("router")->generate("admin.$this->entityName.index"));
        $breadcrumbs->addItem('Borrar');

        $entity = $this->getManager()->find($id);

        $form = $this->createForm(new DeleteUserType($entity));

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('BackendBundle:User');
        $articleRepo = $em->getRepository('BackendBundle:Article');

        $users = new ArrayCollection($userRepo->findByParent($entity));

        $criteria = new Criteria();
        $criteria->where(
            $criteria->expr()->orX(
                $criteria->expr()->eq('owner', $entity),
                $criteria->expr()->eq('supervisor', $entity),
                $criteria->expr()->eq('editor', $entity)
            )
        );
        $articles = $articleRepo->matching($criteria);

        if ($users->count() > 0 || $articles->count() > 0) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'No se puede eliminar un usuario que tenga asignados usuarios o artículos'
            );
        }

        try {
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'No se ha podido eliminar el usuario, ocurrieron errores en el proceso'
            );
        }

        return $this->redirect($this->generateUrl('admin.user.index'));

//        return $this->render('AdminBundle:Default:delete.html.twig', array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//            'metadata' => $this->getMetadata()
//        ));
    }

    public function confirmDeleteAction(Request $request, $id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem($this->entityDescription, $this->get("router")->generate("admin.$this->entityName.index"));
        $breadcrumbs->addItem('Borrar');

        $entity = $this->getManager()->find($id);

        $form = $this->createForm(new DeleteUserType($entity));

        if (!$request->isMethodSafe()) {
            $form->handleRequest($request);

            $userToArticles = $form->get('userToArticles')->getData();
            $userToTeam = $form->get('userToTeam')->getData();

            $formError = new FormError('Ha de seleccionar un usuario para este campo.');
            if (!$userToArticles) $form->get('userToArticles')->addError($formError);
            if (!$userToTeam) $form->get('userToTeam')->addError($formError);

            if ($form->isValid()) {
                //$this->getManager()->delete($entity);
                $this->get('session')->getFlashBag()->add(
                    'success', 'Elemento eliminado correctamente'
                );
                return $this->redirect($this->generateUrl("admin.$this->entityName.index"));
            }
        }

        return $this->render('AdminBundle:Default:delete.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'metadata' => $this->getMetadata()
        ));
    }
}
