<?php

namespace Uzink\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class DefaultController
 * @package Uzink\AdminBundle\Controller
 */
class SecurityController extends \FOS\UserBundle\Controller\SecurityController
{
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderLogin(array $data) {
        $requestAttributes = $this->container->get('request')->attributes;
        if ($requestAttributes->get('_route') == 'admin_login') {
            $template = 'AdminBundle:Default:login.html.twig';
        } else {
            $template = 'FOSUserBundle:Security:login.html.twig';
        }

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
