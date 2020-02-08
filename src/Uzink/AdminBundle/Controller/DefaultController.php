<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package Uzink\AdminBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Homepage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepageAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("admin.homepage"));

        return $this->render('AdminBundle:Default:homepage.html.twig');
    }
}
