<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\CategoryLink;

class LinkController extends Controller
{
    /**
     * Category links index
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Inicio', $this->get('router')->generate('admin.homepage'));
        $breadcrumbs->addItem('Links de categorías');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BackendBundle:CategoryLink');

        $sharpCategories = Article::getSharpCategories();
        $chronicCategories = Article::getChronicCategories();

        $formBuilder = $this->createFormBuilder();

        $options = array(
            'mapped' => false,
            'empty_value' => 'Selecciona una categoría'
        );

        foreach ($sharpCategories as $key => $category) {
            $options = array(
                'mapped' => false,
                'empty_value' => 'Selecciona una categoría'
            );
            $entity = $repo->findOneBySlug($key);
            if ($entity) $options['data'] = $entity->getCategory();

            $formBuilder->add($key, 'category', $options);
        }

        foreach ($chronicCategories as $cKey => $category) {
            foreach ($category as $iKey => $item) {
                $key = $cKey . '_' .$iKey;
                $options = array(
                    'mapped' => false,
                    'empty_value' => 'Selecciona una categoría'
                );
                $entity = $repo->findOneBySlug($key);
                if ($entity) $options['data'] = $entity->getCategory();

                $formBuilder->add($key, 'category', $options);
            }
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($sharpCategories as $key => $category) {
                $field = $form->get($key);
                if ($field->getData()) {
                    $entity = $repo->findOneBySlug($key);

                    if ($entity) {
                        $entity->setCategory($field->getData());
                    } else {
                        $entity = new CategoryLink();
                        $entity->setSlug($key);
                        $entity->setCategory($field->getData());
                    }
                    $em->persist($entity);
                }
            }
            $em->flush();

            foreach ($chronicCategories as $cKey => $category) {
                foreach ($category as $iKey => $item) {
                    $key = $cKey . '_' .$iKey;
                    $field = $form->get($key);
                    if ($field->getData()) {
                        $entity = $repo->findOneBySlug($key);

                        if ($entity) {
                            $entity->setCategory($field->getData());
                        } else {
                            $entity = new CategoryLink();
                            $entity->setSlug($key);
                            $entity->setCategory($field->getData());
                        }
                        $em->persist($entity);
                    }
                }
                $em->flush();
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                'Configuración guardada correctamente'
            );
        }

        return $this->render(
            'AdminBundle:CategoryLinks:index.html.twig',
            [
                'sharpCategories' => $sharpCategories,
                'chronicCategories' => $chronicCategories,
                'form' => $form->createView()
            ]
        );
    }
}