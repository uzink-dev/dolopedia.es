<?php

namespace Uzink\BackendBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Entity\UserRepository;
use Uzink\BackendBundle\Event\ActivityEvent;
use Uzink\BackendBundle\Form\TeamAssignType;
use Uzink\BackendBundle\Form\TeamCreateType;
use Uzink\BackendBundle\Form\UserAccountType;
use Uzink\BackendBundle\Form\UserRegistrationType;
use Uzink\BackendBundle\Form\UserProfileType;
use Uzink\BackendBundle\Handler\UserHandler;
use Uzink\BackendBundle\Manager\UserManager;

class UserController extends ServicesAwareController
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserHandler
     */
    private $handler;

    /**
     * @var UserManager
     */
    private $manager;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        
        $this->em = $this->getDoctrine()->getManager();
        $this->repository = $this->em->getRepository('BackendBundle:User');
        $this->handler = $this->get('uzink.user.handler');
        $this->manager = $this->get('uzink.user.manager');
    }

    //<editor-fold desc="Registration">
    public function registrationOptionsAction() {
        return $this->render('FrontBundle:Registration:registration.options.layout.html.twig', array());
    }
    
    public function registrationDefaultAction(Request $request) {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(new UserRegistrationType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager->updateUser($user);

            return $this->redirect($this->generateUrl('public.home'));
        }

        return $this->render('FrontBundle:Registration:registration.default.layout.html.twig', array('form' => $form->createView()));        
    }
    //</editor-fold>

    //<editor-fold desc="Basic Actions">
    public function showAction($id) {
        $userManager = $this->get('uzink.user.manager');
        $user = $userManager->get($id);
        $items = array(
            array($user)
        );
        $this->handler->makeBreadcrumb($items);

        return $this->render(
            'FrontBundle:User:public.layout.show.html.twig',
            [
                'user' => $user
            ]
        );
    }

    public function showProfileAction() {
        $items = array(
            array('profile.title')
        );
        $this->handler->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:panel.layout.profile.show.html.twig', array());
    }

    public function editProfileAction(Request $request) {
        $user = $this->getUser();

        $form = $this->createForm(new UserProfileType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

            $this->em->flush();

            return $this->redirect($this->generateUrl('panel.user.profile.show'));
        } else {
            $this->em->refresh($user);
        }

        $items = array(
            array('profile.title', 'panel.user.profile.show'),
            array('profile.edit')
        );
        $this->handler->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:panel.layout.profile.edit.html.twig', array('form' => $form->createView()));
    }

    public function showAccountAction() {
        $items = array(
            array('account.title')
        );
        $this->handler->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:panel.layout.account.show.html.twig', array());
    }

    public function editAccountAction(Request $request) {
        $user = $this->getUser();

        $form = $this->createForm(new UserAccountType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

            return $this->redirect($this->generateUrl('panel.user.account.show'));
        } else {
            $this->em->refresh($user);
        }

        $items = array(
            array('account.title', 'panel.user.account.show'),
            array('account.edit')
        );
        $this->handler->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:panel.layout.account.edit.html.twig', array('form' => $form->createView()));
    }
    //</editor-fold>

    //<editor-fold desc="Team">
    public function showTeamAction($id = null) {
        $items = array();

        if (!$id) {
            $user = $this->getUser();
            $items[] = array('team.title');
        } else {
            $user = $this->manager->get($id);
            $items[] = array('team.title', 'workflow.team.show');
            $teamDes = $this->get('translator')->trans('team.of', array('%user%' => $user), 'dolopedia');
            $items[] = array($teamDes);
        }

        $this->handler->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:workflow.layout.team.show.html.twig', array('user' => $user));
    }

    public function showTeamDetailAction($id) {
        $user = $this->manager->get($id);
        $items[] = array('team.title', 'workflow.team.show');
        $items[] = array($user);

        $this->handler->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:workflow.layout.team.detail.html.twig', array('user' => $user));
    }

    public function createTeamAction(Request $request) {
        $userManager = $this->get('fos_user.user_manager');
        $currentUser = $this->getUser();
        $user = $userManager->createUser();
        $translator = $this->get('translator');
        $form = $this->createForm(new TeamCreateType($currentUser, $this->get('translator')), $user);

        if (!$request->isMethodSafe()) {
            $form->handleRequest($request);

            $role = $form->get('type')->getData();
            $parentUser = $form->get('roleSelect')->get($role)->getData();

            $blankError = new FormError($translator->trans('form.error.notBlank', array(), 'dolopedia'));
            if (empty($role)) $form->get('type')->addError($blankError);
            if (empty($parentUser)) $form->get('roleSelect')->get($role)->addError($blankError);

            if ($form->isValid()) {
                $user->addRole($role);
                $user->setParent($parentUser);

                $dispatcher = $this->container->get('event_dispatcher');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                switch ($role) {
                    case User::ROLE_SUPER_ADMIN:
                    case User::ROLE_ADMIN:
                        $receivers = $this->repository->findUsersByRole(User::ROLE_ADMIN);
                        $eventType = Activity::TYPE_USER_NEW_ADMIN;
                        break;
                    case User::ROLE_LEADER:
                        $receivers = array();
                        $receivers[] = $currentUser;
                        $receivers[] = $user;
                        $eventType = Activity::TYPE_USER_NEW_LEADER;
                        break;
                    case User::ROLE_SUPERVISOR:
                        $receivers = array();
                        $receivers[] = $currentUser;
                        $receivers[] = $user;
                        $eventType = Activity::TYPE_USER_NEW_SUPERVISOR;
                        break;
                    case User::ROLE_EDITOR:
                        $receivers = array();
                        $receivers[] = $currentUser;
                        $receivers[] = $user;
                        $eventType = Activity::TYPE_USER_NEW_EDITOR;
                        break;
                    default:
                        $receivers = array();
                        $receivers[] = $currentUser;
                        $receivers[] = $user;
                        $eventType = Activity::TYPE_USER_NEW_TEAM_USER;
                }
                $event = new ActivityEvent($user, $eventType, $receivers, $currentUser);
                $this->get('event_dispatcher')->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

                return $this->redirect($this->generateUrl('workflow.team.show'));
            }
        }

        $items = array(
            array('team.title', 'workflow.team.show'),
            array('team.new')
        );
        $this->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:workflow.layout.team.create.html.twig', array('form' => $form->createView()));
    }

    public function assignTeamAction(Request $request) {
        $user = $this->getUser();
        $userManager = $this->get('uzink.user.manager');
        $articleManager = $this->get('uzink.article.manager');
        $repository = $this->getDoctrine()->getManager()->getRepository('BackendBundle:User');
        $form = $this->createForm(new TeamAssignType($user, $this->get('translator'), $repository), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $assignedUser */
            $assignedUser = $form->get('user')->getData();
            $role = $form->get('type')->getData();
            $parentUser = $form->get('roleSelect')->get($role)->getData();

            $assignedUser->addRole($role);
            $assignedUser->setParent($parentUser);

            $articles = $form->get('articles')->getData();

            /** @var Article $article */
            foreach ($articles as $article) {
                $assignedUser->addAssignedArticle($article);
                $article->setEditor($assignedUser);
                $articleManager->save($article);
            }

            $userManager->save($assignedUser);

            switch ($role) {
                case User::ROLE_SUPER_ADMIN:
                case User::ROLE_ADMIN:
                    $receivers = $this->repository->findUsersByRole(User::ROLE_ADMIN);
                    $eventType = Activity::TYPE_USER_NEW_ADMIN;
                    break;
                case User::ROLE_LEADER:
                    $receivers = array();
                    $receivers[] = $assignedUser;
                    $receivers[] = $user;
                    $eventType = Activity::TYPE_USER_NEW_LEADER;
                    break;
                case User::ROLE_SUPERVISOR:
                    $receivers = array();
                    $receivers[] = $assignedUser;
                    $receivers[] = $user;
                    $eventType = Activity::TYPE_USER_NEW_SUPERVISOR;
                    break;
                case User::ROLE_EDITOR:
                    $receivers = array();
                    $receivers[] = $assignedUser;
                    $receivers[] = $user;
                    $eventType = Activity::TYPE_USER_NEW_EDITOR;
                    break;
                default:
                    $receivers = array();
                    $receivers[] = $assignedUser;
                    $receivers[] = $user;
                    $eventType = Activity::TYPE_USER_NEW_TEAM_USER;
            }

            $event = new ActivityEvent($assignedUser, $eventType, $receivers, $user);
            $this->get('event_dispatcher')->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

            return $this->redirect($this->generateUrl('workflow.team.show'));
        }

        $items = array(
            array('team.title', 'workflow.team.show'),
            array('team.new')
        );
        $this->makeBreadcrumb($items);

        return $this->render('FrontBundle:User:workflow.layout.team.assign.html.twig', array('form' => $form->createView()));
    }

    public function changeRoleAction(Request $request, $id, $role) {
        /** @var User $user */
        $user = $this->manager->get($id);
        $currentUser = $this->getUser();

        if ($currentUser->isWorkgroupUser($user)) {
            $em = $this->getDoctrine()->getManager();
            $user->setRoles(array($role));
            $em->persist($user);
            $em->flush();
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
    //</editor-fold>

    //<editor-fold desc="Contacts">
    public function indexContactAction(Request $request) {
        $items = array(
            array('contact.title')
        );
        $this->makeBreadcrumb($items);

        $pager = $this->getContactPager($request);

        return $this->render('FrontBundle:User:panel.layout.contact.index.html.twig', ['pager' => $pager]);
    }

    public function showContactAction($id) {
        $userManager = $this->get('uzink.user.manager');
        $contact = $userManager->get($id);

        $items = array(
            array('contact.title', 'panel.user.contact.index'),
            array($contact)
        );
        $this->makeBreadcrumb($items);

        return $this->render(
            'FrontBundle:User:panel.layout.contact.show.html.twig',
            array(
                'user' => $contact
            )
        );
    }

    public function followToggleContactAction(User $contact, Request $request)
    {
        try {
            $user = $this->getUser();
            $contacts = $user->getContacts();

            if ($contacts->contains($contact)) {
                $user->removeContact($contact);

                $eventName = Activity::TYPE_USER_UNFOLLOW;
                $receivers = array($user);
            } else {
                $user->addContact($contact);

                $eventName = Activity::TYPE_USER_FOLLOW;
                $receivers = array($user, $contact);
            }

            $event = new ActivityEvent($contact, $eventName, $receivers, $user);
            $this->get('event_dispatcher')->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

            if ($request->isXmlHttpRequest()) {
                $htmlCode = $this->get('twig')->render('FrontBundle:Component:partial.user.imageBox.html.twig', ['user' => $contact]);

                return new JsonResponse(array('status' => true, 'html' => $htmlCode));
            }

            return $this->redirect($request->headers->get('referer'));
        } catch (\Exception $e) {
            return new JsonResponse(array('status' => false));
        }
    }

    private function getContactPager(Request $request)
    {
        $page = $request->get('page', 1);
        $alphabetFilter = $request->get('filter', 'all');
        $roleFilter = $request->get('role', 'all');

        $repo = $this->getDoctrine()->getRepository('BackendBundle:User');
        switch ($roleFilter) {
            case 'all':
                $qb = $repo->createQueryBuilder('u');
                break;
            default:
                $qb = $repo->findUsersByRoleQB($roleFilter, null, true);
        }

        $qb->andWhere($qb->expr()->in('u', ':contacts'))
           ->setParameter('contacts', $this->getUser()->getContacts(), 'array');

        switch ($alphabetFilter) {
            case 'digit':
                $orClause = $qb->expr()->orX();
                $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                foreach ($digits as $digit) {
                    $orClause->add(
                        $qb->expr()->like(
                            'u.name',
                            $qb->expr()->literal($digit.'%'))
                    );
                }

                $qb->andWhere($orClause);
                break;
            case 'all':
                break;
            default:
                $qb->andWhere(
                    $qb->expr()->like('u.name', $qb->expr()->literal($alphabetFilter.'%'))
                );
        }

        $result = $qb->getQuery()->getResult();

        $adapter = new ArrayAdapter($result);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('pager_per_page'));
        $pager->setCurrentPage($page);

        return $pager;
    }
    //</editor-fold>

    //<editor-fold desc="Activity Actions">
    public function getActivitiesAction($page)
    {
        $repo = $this->getDoctrine()->getRepository('BackendBundle:Activity');
        $criteria = [
            'receiver' => $this->getUser()
        ];
        $activities = $repo->findBy($criteria, array('createdAt' => 'DESC'));

        $pager = $this->getActivityPager($activities, $page);

        $last = false;
        if ( $page == $pager->getNbPages()) $last = true;

        $html = $this->renderView(
            'FrontBundle:User/Component:widget.recent.activity.partial.html.twig',
            array(
                'pager' => $pager,
            )
        );

        $results = array(
            'page' => $page,
            'last' => $last,
            'html' => $html
        );

        return new JsonResponse($results);
    }

    public function renderActivityAction($page)
    {
        $repo = $this->getDoctrine()->getRepository('BackendBundle:Activity');
        $criteria = [
            'receiver' => $this->getUser()
        ];
        $activities = $repo->findBy($criteria, array('createdAt' => 'DESC'));

        $pager = $this->getActivityPager($activities, $page);

        return $this->render(
            'FrontBundle:User/Component:widget.recent.activity.html.twig',
            array(
                'pager' => $pager,
            )
        );
    }

    private function getActivityPager($activities, $page)
    {
        $adapter = new ArrayAdapter($activities);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('pager_per_activities'));
        $pager->setCurrentPage($page);

        return $pager;
    }
    //</editor-fold>
}
