<?php

namespace Uzink\BackendBundle\Controller;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Comment;
use Uzink\BackendBundle\Entity\Draft;
use Uzink\BackendBundle\Entity\Rating;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Search\ArticleSearch;
use Uzink\BackendBundle\Search\Form\SearchType;

class CommentController extends Controller
{
    public function indexAction() {
        // Make the breadcrumbs
        $items = array(
            array('article.myComments')
        );
        $this->get('uzink.user.handler')->makeBreadcrumb($items);

        return $this->render('FrontBundle:Comment:panel.layout.index.html.twig');
    }

    public function editAction(Request $request, $id) {
        // Checks if the user is allowed to edit the comment
        $manager = $this->get('uzink.comment.manager');
        $comment = $manager->get($id);
        $securityContext = $this->get('security.context');

        if (false === $securityContext->isGranted('EDIT', $comment)) {
            throw new AccessDeniedException('Access denied!');
        }

        // Make the breadcrumbs
        $items = array(
            array('article.myComments', 'panel.comment.index'),
            array('article.editComment')
        );
        $this->get('uzink.user.handler')->makeBreadcrumb($items);

        $form = $this->createForm($manager->getForm(), $comment);
        $form->get('_target')->setData($request->headers->get('referer'));

        $form->handleRequest($request);
        $referer = $form->get('_target')->getData();

        if (!$request->isMethodSafe()) {
            if ($form->isValid()) {
                $manager->save($comment);
                return $this->redirect($referer);
            }
        }

        return $this->render(
            'FrontBundle:Comment:panel.layout.edit.html.twig',
            array(
                'comment'   => $comment,
                'form'      => $form->createView(),
                'referer'   => $referer
            )
        );
    }

    public function addAction(Request $request, $id, $scope = null) {
        $articleManager = $this->get('uzink.article.manager');
        $article = $articleManager->get($id);

        $manager = $this->get('uzink.comment.manager');
        $comment = new Comment();
        $form = $this->createForm($manager->getForm(), $comment);

        if (!$request->isMethodSafe()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();

                $comment->setOwner($user);
                $comment->setArticle($article);
                $comment->setScope($scope);

                $manager->save($comment);

                $manager->setPermissions($comment, array('owner'));
                $manager->setPermissions($comment, array('owner'), User::ROLE_ADMIN);
            }
        }

        return $this->render(
            'FrontBundle:Article:partial.layout.comment.html.twig',
            array(
                'article' => $article,
                'form' => $form->createView(),
                'scope' => $scope
            )
        );
    }

    public function removeAction(Request $request, $id) {
        // Checks if the user is allowed to delete the comment
        $manager = $this->get('uzink.comment.manager');
        $comment = $manager->get($id);
        $securityContext = $this->get('security.context');

        if (false === $securityContext->isGranted('DELETE', $comment)) {
            throw new AccessDeniedException();
        }

        $manager->delete($comment);

        return $this->redirect($request->headers->get('referer'));
    }
}
