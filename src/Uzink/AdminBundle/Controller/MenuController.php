<?php

namespace Uzink\AdminBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends AdminController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->entityName = 'menu';
        $this->entityDescription = 'Menús';
        $this->entityFields = array(
            'title' => 'Nombre'
        );
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

        $originalItems = new ArrayCollection();
        foreach ($entity->getMenuItems() as $menuItem) {
            $originalItems->add($menuItem);
        }

        $form->handleRequest($request);
        if ($form->isValid()) {

            foreach ($originalItems as $menuItem) {
                if (false === $entity->getMenuItems()->contains($menuItem)) {
                    $menuItem->setMenu(null);
                    $this->getManager()->getEntityManager()->remove($menuItem);
                }
            }

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

}
