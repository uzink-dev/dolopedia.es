<?php

namespace Uzink\BackendBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Uzink\BackendBundle\Entity\Contact;
use Uzink\BackendBundle\Form\ContactType;

class CmsController extends Controller {
    public function staticAction($slug) {
        $userHandler = $this->get('uzink.user.handler');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BackendBundle:Page');

        $criteria = array(
            'seoSlug' => $slug
        );
        $page = $repo->findOneBy($criteria);
        if (!$page) throw new NotFoundHttpException('Resource Not Found');

        $items = array(
            array($page->getTitle())
        );
        $userHandler->makeBreadcrumb($items);

        return $this->render('FrontBundle:Cms:public.layout.static.page.html.twig', array('page' => $page));
    }

    public function linkHelpPageAction() {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BackendBundle:HelpPage');

        $role = $this->getUser()->getRole();

        $criteria = array(
            'role' => $role
        );

        $pages = $repo->findBy($criteria, array('position' => 'ASC'));

        if ($pages) return $this->render('FrontBundle:HelpPage:panel.link.html.twig', array());
        else return new Response();
    }

    public function showHelpPageAction($slug = null) {
        $userHandler = $this->get('uzink.user.handler');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BackendBundle:HelpPage');

        $role = $this->getUser()->getRole();

        $criteria = array(
            'role' => $role
        );

        $pages = $repo->findBy($criteria, array('position' => 'ASC'));
        if (!$pages) throw new NotFoundHttpException('Resource Not Found');

        $helpPage = null;
        if ($slug) {
            foreach ($pages as $page) {
                if ($page->getSeoSlug() == $slug) $helpPage = $page;
            }
            if (!$helpPage) throw new NotFoundHttpException('Resource Not Found');
        }

        if (!$helpPage) $helpPage = reset($pages);

        $items = array(
            array($helpPage->getTitle())
        );
        $userHandler->makeBreadcrumb($items);

        return $this->render(
            'FrontBundle:HelpPage:public.layout.show.html.twig',
            array(
                'helpPage'  => $helpPage,
                'pages'     => $pages
            )
        );
    }

    public function contactAction(Request $request)
    {
        $userHandler = $this->get('uzink.user.handler');
        $items = array(
            array('Contacto')
        );
        $userHandler->makeBreadcrumb($items);

        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', $this->get('translator')->trans('contact_page.success', array(), 'dolopedia'));
            $contact = new Contact();
            $form = $this->createForm(new ContactType(), $contact);
        }

        return $this->render('@Front/Cms/public.layout.contact.page.html.twig', array(
            'form' => $form->createView()
        ));
    }
}