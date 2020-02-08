<?php

namespace Uzink\BackendBundle\Controller;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Uzink\BackendBundle\Entity\Category;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Form\ArticleRequestAcceptType;
use Uzink\BackendBundle\Form\ArticleRequestDeclineType;
use Uzink\BackendBundle\Form\ArticleRequestType;
use Uzink\BackendBundle\Handler\WorkflowHandler;
use Uzink\BackendBundle\Manager\ArticleManager;
use Uzink\BackendBundle\Manager\RequestManager;
use Uzink\BackendBundle\Search\Form\SearchType;
use Uzink\BackendBundle\Search\RequestSearch;
use Uzink\BackendBundle\Security\Permission\PermissionMap;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RequestController extends ServicesAwareController
{
    /**
     * @var ArticleManager
     */
    protected $articleManager;

    /**
     * @var RequestManager
     */
    protected $requestManager;

    /**
     * @var WorkflowHandler
     */
    protected $workflowHandler;

    /**
     * @var EntityManager
     */
    private $em;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $this->getDoctrine()->getManager();
        $this->workflowHandler = $this->get('uzink.workflow.handler');
        $this->articleManager = $this->get('uzink.article.manager');
        $this->requestManager = $this->get('uzink.request.manager');
    }

    /**
     * Shows the request list and manages the filtering and pagination
     *
     * @param HttpRequest $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function listRequestsAction(HttpRequest $request){
        $items = array(
            array('request.title')
        );
        $this->makeBreadcrumb($items);

        $requestSearchForm = $this->createForm(new SearchType(RequestSearch::$sortChoices));
        $requestSearch = new RequestSearch($this->getUser(), $requestSearchForm);

        $requestSearch->handleRequest($request);
        $requests = $this->getDoctrine()->getRepository('BackendBundle:Request')->searchRequest($requestSearch);

        return $this->render('FrontBundle:Request:workflow.layout.list.html.twig', array(
            'requestSent'       => $requests['sent'],
            'requestReceived'   => $requests['received'],
            'requestSearchForm' => $requestSearch->getForm()->createView()
        ));
    }

    /**
     * @param Article|Category $entity
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createRequestFormAction($entity) {
        switch (true) {
            case $entity instanceof Category:
                $request = $this->requestManager->create(Request::TYPE_REQUEST_NEW, $entity);
                $requestForm = new ArticleRequestType();
                break;

            case $entity instanceof Article:
                $request = $this->requestManager->create(Request::TYPE_REQUEST_MODIFY, $entity);
                $requestForm = new ArticleRequestType($entity);
                break;

            default:
                throw new \Exception('This type are not supported');
                break;
        }

        $form = $this->createForm($requestForm, $request, array(
            'action' => $this->generateUrl('workflow.request.new')
        ));

        return $this->render('FrontBundle:Article:workflow.popup.request.html.twig',
            array(
                'formRequest' => $form->createView()
            ));
    }

    /**
     * Handles the response from the FrontEnd
     *
     * @param HttpRequest $httpRequest
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createRequestAction(HttpRequest $httpRequest) {
        $request = new Request();
        $requestForm = new ArticleRequestType();

        $form = $this->createForm($requestForm, $request, array(
            'action' => $this->generateUrl('workflow.request.new')
        ));
        $form->handleRequest($httpRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->requestManager->save($request);
            $this->workflowHandler->startAndSave($request);

            return new JsonResponse(true);
        }

        return new JsonResponse(false);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // - Request Handling ----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @Security("is_granted('VIEW', request)")
     */
    public function responseRequestAction(Request $request) {
        $article = $request->getArticle();
        $title = $article ? $article->getTitle() : $request->getTitle();

        $items = array(
            array('request.title', 'workflow.request.list'),
            array($title)
        );
        $this->makeBreadcrumb($items);

        return $this->render('FrontBundle:Request:workflow.layout.detail.html.twig', array(
            'request' => $request
        ));
    }

    /**
     * Draw and handles the request acceptance
     *
     * @param Request $request
     * @param HttpRequest $HttpRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('OWNER', request)")
     */
    public function acceptRequestAction(Request $request, $popup = false, HttpRequest $HttpRequest) {
        $requestForm = new ArticleRequestAcceptType($request, $this->get('translator'));
        $formAction = $this->generateUrl('workflow.request.accept',array('id' => $request->getId()));
        $form = $this->createForm($requestForm, $request, array('action' => $formAction));

        $form->handleRequest($HttpRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->workflowHandler->reachNextState($request, Request::STEP_ACCEPT);

            // TODO::Enviar un mensaje al usuario con las causas de reusar la solicitud
            return $this->redirect($this->generateUrl('workflow.request.response', array('id' => $request->getId())));
        }

        return $this->render('FrontBundle:Request:workflow.form.accept.html.twig',
            array(
                'form'  => $form->createView(),
                'popup' => $popup
            ));
    }

    /**
     * Draw and handles the request refused
     *
     * @param Request $request
     * @param HttpRequest $httpRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('OWNER', request)")
     */
    public function declineRequestAction(Request $request, $popup = false, HttpRequest $httpRequest) {
        $requestForm = new ArticleRequestDeclineType($request, $this->get('translator'));
        $formAction = $this->generateUrl('workflow.request.decline',array('id' => $request->getId()));
        $form = $this->createForm($requestForm, $request, array('action' => $formAction));

        $form->handleRequest($httpRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->workflowHandler->reachNextState($request, Request::STEP_DECLINE);

            return $this->redirect($this->generateUrl('workflow.request.response', array('id' => $request->getId())));
        }

        return $this->render('FrontBundle:Request:workflow.form.decline.html.twig',
            array(
                'form'  => $form->createView(),
                'popup' => $popup
            ));
    }

    // -----------------------------------------------------------------------------------------------------------------
    // - Article Request Handling --------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Carries the request to the next state and creates a new article with this action
     *
     * @param Request $request
     * @param HttpRequest $httpRequest
     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Security("is_granted('OWNER', request)")
     */
    public function createArticleAction(Request $request, HttpRequest $httpRequest) {
        if ($httpRequest->getMethod() == 'POST') {
            // TODO-Security::Resource modifications based in credentials

            if ($request->getStatus() == Request::STATUS_EDITION_REQUESTED && $request->getType() == Request::TYPE_EDITION_CREATION) {
                $this->workflowHandler->reachNextState($request, Request::STEP_CREATE);
                $this->em->refresh($request);

                $article = $request->getArticle();

                return $this->redirect($this->generateUrl('workflow.article.edit', array('id' => $article->getId())));
            }
        }

        return null;
    }

    /**
     * Carries the request to the next state and creates a new article with this action
     *
     * @param Request $request
     * @param HttpRequest $httpRequest
     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Security("is_granted('OWNER', request)")
     */
    public function modifyArticleAction(Request $request, HttpRequest $httpRequest) {
        if ($httpRequest->getMethod() == 'POST') {
            // TODO-Security::Resource modifications based in credentials

            if ($request->getStatus() == Request::STATUS_EDITION_REQUESTED) {
                $this->workflowHandler->reachNextState($request, Request::STEP_MODIFY);
            }

            $article = $request->getArticle();

            return $this->redirect($this->generateUrl('workflow.article.edit', array('id' => $article->getId())));
        }

        return null;
    }
}

