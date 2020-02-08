<?php

namespace Uzink\RestBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations\View;


class BibliographyController extends FOSRestController
{
    
// <editor-fold defaultstate="collapsed" desc="[GET] /bibligraphyentry">
    /**
     * @View(
     *      serializerGroups={"detail"}
     * )
     */
        public function getBibliographyAction($id) {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('BackendBundle:BibliographicEntry');

            $bibliographicEntry = $repo->find($id);
            
            if (!$bibliographicEntry) throw new NotFoundHttpException();

            return $bibliographicEntry;
        }

// </editor-fold>
}