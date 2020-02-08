<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    protected $entityName = '';
    protected $entityDescription = '';
    protected $entityFields = array();

    /**
     * Get entity manager.
     *
     */
    public function getManager()
    {
        $manager = $this->container->get("admin.$this->entityName.manager");

        return $manager;
    }

    public function getForm($entity) {
        $formClass = 'Uzink\\AdminBundle\\Form\\' . ucfirst($this->entityName) . 'Type';
        $form = $this->createForm(new $formClass(), $entity);

        return $form;
    }

    public function getMetadata()
    {
        $metadata = array();

        $metadata['entityName'] = $this->entityName;
        $metadata['entityDescription'] = $this->entityDescription;
        $metadata['entityFields'] = $this->entityFields;

        return $metadata;
    }

    /**
     * Lists all entities.
     */
    public function indexAction($page)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem($this->entityDescription);

        $pager = $this->getManager()->getPager($page, $this->container->getParameter('admin.list_max_per_page'));

        return $this->render('AdminBundle:Default:index.html.twig', array(
            'pager' => $pager,
            'metadata' => $this->getMetadata()
        ));
    }

    /**
     * Creates a new entity.
     */
    public function createAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem($this->entityDescription, $this->get("router")->generate("admin.$this->entityName.index"));
        $breadcrumbs->addItem('Nuevo');

        $entity = $this->getManager()->create();
        $form = $this->getForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
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
     * Displays a form to create a new Banner entity.
     */
    public function newAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem($this->entityDescription, $this->get("router")->generate("admin.$this->entityName.index"));
        $breadcrumbs->addItem('Nuevo');

        $entity = $this->getManager()->create();
        $form = $this->getForm($entity);

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
        $breadcrumbs->addItem($this->entityDescription, $this->get("router")->generate("admin.$this->entityName.index"));
        $breadcrumbs->addItem('Editar');

        if (!$entity) {
            throw $this->createNotFoundException('No se ha encontrado el elemento');
        }

        $form = $this->getForm($entity);

        return $this->render('AdminBundle:Default:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'metadata' => $this->getMetadata()
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


    /**
     * Delete
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->getManager()->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add(
                'error', 'Elemento no encontrado, no ha podido eliminarse'
            );
        } else {
            try {
                $this->getManager()->delete($entity);

                $this->get('session')->getFlashBag()->add(
                    'success', 'Elemento eliminado correctamente'
                );
            } catch (\Exception $ex) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'No se ha podido completar el borrado, ocurrieron errores en el proceso, es posible que tenga elementos dependientes que no se pueden ser eliminados automáticamente'
                );
            }
        }
        return $this->redirect($this->generateUrl("admin.$this->entityName.index"));
    }
}