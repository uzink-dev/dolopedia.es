<?php

namespace Uzink\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Form\MessageMultipleType;
use Uzink\BackendBundle\Form\MessageResponseType;
use Uzink\BackendBundle\Form\MessageType;
use Uzink\BackendBundle\Entity\Message;
use Uzink\BackendBundle\Search\Form\MessageSearchType;
use Uzink\BackendBundle\Search\Form\SearchType;
use Uzink\BackendBundle\Search\MessageSearch;

class MessageController extends ServicesAwareController {
    public function indexAction(Request $request) {
        $messageSearch = $this->get('uzink.message.search');

        $items = array(
            array('message.title')
        );
        $this->makeBreadcrumb($items);

        $messageSearch->handleRequest($request);
        $pagers = $messageSearch->getPager();

        //dump($pagers);

        return $this->render('FrontBundle:Message:panel.layout.index.html.twig',
            [
                'sentPager'         => $pagers[Message::SENT],
                'receivedPager'     => $pagers[Message::RECEIVED],
                'sentFilter'        => $messageSearch->getFilters(Message::SENT),
                'receivedFilter'    => $messageSearch->getFilters(Message::RECEIVED)
            ]
        );
    }

    public function showAction(Request $request, $id) {
        $messageManager = $this->get('uzink.message.manager');
        $user = $this->getUser();

        /** @var Message $message */
        $message = $messageManager->get($id);
        $securityContext = $this->get('security.context');

        if (false === $securityContext->isGranted('VIEW', $message)) {
            throw new AccessDeniedException();
        }

        if ($user == $message->getReceiver() && !$message->getMultiple() && !$message->getReaded())
            $messageManager->readed($message);

        $messageMetadata = $message->getMultipleSendMetadata();
        if ($message->getMultiple() && !$messageMetadata['readed'][$this->getUser()->getId()]) {
            $messageMetadata['readed'][$this->getUser()->getId()] = true;

            $message->setMultipleSendMetadata($messageMetadata);
            $messageManager->save($message);
        }

        $items = array(
            array('message.title', 'panel.message.index'),
            array($message->getSubject())
        );
        $this->makeBreadcrumb($items);

        $responseMessage = $messageManager->createResponse($message);
        $form = $this->createForm(new MessageResponseType($user), $responseMessage);

        if (!$request->isMethodSafe()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $messageManager->save($responseMessage);

                $messageManager->setPermissions($responseMessage, array('owner'), $responseMessage->getSender());
                $messageManager->setPermissions($responseMessage, array('owner'), $responseMessage->getReceiver());

                return $this->redirect($this->generateUrl('panel.message.index'));
            }
        }

        return $this->render('FrontBundle:Message:panel.layout.show.html.twig',
            array(
                'message' => $message,
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param null $receiver
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction($receiver = null, Request $request) {
        $messageManager = $this->get('uzink.message.manager');
        $userManager = $this->get('uzink.user.manager');

        $user = $this->getUser();
        $toUser = null;
        if ($receiver) {
            $toUser = $userManager->get($receiver);
            if (!$user->getWorkgroupUsers()->contains($toUser)) $this->createAccessDeniedException();
        }

        $message = $messageManager->create();
        if ($toUser) $message->setReceiver($toUser);

        $items = array(
            array('message.title', 'panel.message.index'),
            array('message.new')
        );
        $this->makeBreadcrumb($items);

        $form = $this->createForm(new MessageType($user), $message);

        if (!$request->isMethodSafe() && $form->handleRequest($request) && $form->isValid()) {
            $message->setSender($user);
            $messageManager->save($message);

            $messageManager->setPermissions($message);

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('message.sent', array(), 'dolopedia')
            );

            return $this->redirect($this->generateUrl('panel.message.index'));
        }

        return $this->render('FrontBundle:Message:panel.layout.new.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newMultipleAction(Request $request) {
        $messageManager = $this->get('uzink.message.manager');

        $user = $this->getUser();
        $message = $messageManager->create();

        $items = array(
            array('message.title', 'panel.message.index'),
            array('message.new_multiple')
        );
        $this->makeBreadcrumb($items);

        $form = $this->createForm(new MessageMultipleType($user), $message);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($user);
            $messageManager->save($message);
            $messageManager->setPermissions($message);

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('message.multiple_sent', array(), 'dolopedia')
            );

            return $this->redirect($this->generateUrl('panel.message.index'));
        }

        return $this->render('FrontBundle:Message:panel.layout.multiple.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
} 