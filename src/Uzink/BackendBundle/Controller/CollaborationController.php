<?php

namespace Uzink\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CollaborationController extends Controller {
    public function indexAction(Request $request) {
        $userHandler = $this->get('uzink.user.handler');

        $items = array(
            array('collaboration.title')
        );
        $userHandler->makeBreadcrumb($items);

        return $this->render('FrontBundle:Collaboration:panel.layout.index.html.twig');
    }
}