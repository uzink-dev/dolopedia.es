<?php

namespace Uzink\ConfiguracionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Uzink\ConfiguracionBundle\Entity\ParametroConfiguracion;
use Uzink\ConfiguracionBundle\Form\ParametroConfiguracionType;

/**
 * ParametroConfiguracion controller.
 *
 */
class ParametroConfiguracionController extends Controller
{
    /**
     * Get ParametroConfiguracion manager.
     *
     */
    public function getManager()
    {
        $manager = $this->container->get('parametroconfiguracion_bundle.manager.parametroconfiguracion');

        return $manager;
    }

    /**
     * Lists all ParametroConfiguracion entities.
     *
     */
    public function indexAction($page)
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");    
        $breadcrumbs->addItem("Parametros de configuración", $this->get("router")->generate("admin_configuracion_parametros"));
        
        $pager = $this->getManager()->getPager($page, $this->container->getParameter('back.default_list_maxperpage'));
        

        
        return $this->render('ConfiguracionBundle:ParametroConfiguracion:index.'.$this->get('template.selector')->getTemplate('html').'.twig', array(
            'pager' => $pager,
        ));
    }
    /**
     * Creates a new ParametroConfiguracion entity.
     *
     */
    public function createAction(Request $request)
    {
   
    
        $entity = $this->getManager()->createParametroConfiguracion();
        $form = $this->createForm(new ParametroConfiguracionType(), $entity);
        $form->bind($request);

        $breadcrumbs = $this->get("white_october_breadcrumbs");    
        $breadcrumbs->addItem("Parametros de configuración", $this->get("router")->generate("admin_configuracion_parametros"));
        $breadcrumbs->addItem("Crear nuevo");
        
        if ($form->isValid()) {
            $entity = $this->getManager()->saveParametroConfiguracion($entity);
            $this->get('session')->getFlashBag()->add(
                    'success',
                    'Elemento creado correctamente'
                );
            return $this->redirect($this->generateUrl('admin_configuracion_parametros_edit', array('id' => $entity->getId())));        
        }
        else
        {
            $this->get('session')->getFlashBag()->add(
                                'error',
                                'Ha ocurrido un error creando el elemento'
                            );
        }

        return $this->render('ConfiguracionBundle:ParametroConfiguracion:new.'.$this->get('template.selector')->getTemplate('html').'.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new ParametroConfiguracion entity.
     *
     */
    public function newAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");    
        $breadcrumbs->addItem("Parametros de configuración", $this->get("router")->generate("admin_configuracion_parametros_new"));
        $breadcrumbs->addItem("Crear nuevo");

        $entity = new ParametroConfiguracion();
        $form   = $this->createForm(new ParametroConfiguracionType(), $entity);

        return $this->render('ConfiguracionBundle:ParametroConfiguracion:new.'.$this->get('template.selector')->getTemplate('html').'.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ParametroConfiguracion entity.
     *
     */
    public function editAction($id)
    {
    
        $entity = $this->getManager()->findParametroConfiguracion($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ParametroConfiguracion entity.');
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");    
        $breadcrumbs->addItem("Parametros de configuración", $this->get("router")->generate("admin_configuracion_parametros"));
        $breadcrumbs->addItem($entity->getId(), $this->get("router")->generate("admin_configuracion_parametros_edit", array('id' => $entity->getId())));
        
        $editForm = $this->createForm(new ParametroConfiguracionType(), $entity);
        

        return $this->render('ConfiguracionBundle:ParametroConfiguracion:edit.'.$this->get('template.selector')->getTemplate('html').'.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            
        ));
    }

    /**
     * Edits an existing ParametroConfiguracion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getManager()->findParametroConfiguracion($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ParametroConfiguracion entity.');
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");    
        $breadcrumbs->addItem("Parametros de configuración", $this->get("router")->generate("admin_configuracion_parametros"));
        $breadcrumbs->addItem($entity->getId(), $this->get("router")->generate("admin_configuracion_parametros_edit", array('id' => $entity->getId())));
        
        $editForm = $this->createForm(new ParametroConfiguracionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity = $this->getManager()->updateParametroConfiguracion($entity);
            $this->get('session')->getFlashBag()->add(
                    'success',
                    'Elemento actualizado correctamente'
                );
            return $this->redirect($this->generateUrl('admin_configuracion_parametros_edit', array('id' => $id)));
        }
        else
        {
            $this->get('session')->getFlashBag()->add(
                                'error',
                                'Ha ocurrido un error modificando el elemento'
                            );
        }
        return $this->render('ConfiguracionBundle:ParametroConfiguracion:edit.'.$this->get('template.selector')->getTemplate('html').'.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            
        ));
    }
    /**
     * Deletes a ParametroConfiguracion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConfiguracionBundle:ParametroConfiguracion')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add(
                                'error',
                                'Elemento no encontrado, no ha podido eliminarse'
                            );
        }
        else {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                    'success',
                    'Elemento eliminado correctamente'
                );
        }

        return $this->redirect($this->generateUrl('admin_configuracion_parametros'));
    }

}
