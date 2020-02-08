<?php

namespace Uzink\BackendBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Query;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\ArticleRepository;
use Uzink\BackendBundle\Entity\CategoryRepository;
use Uzink\BackendBundle\Entity\Draft;
use Uzink\BackendBundle\Entity\Rating;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Event\ActivityArticleFavouriteEvent;
use Uzink\BackendBundle\Event\ActivityEvent;
use Uzink\BackendBundle\Form\SearchCondensedType;
use Uzink\BackendBundle\Handler\WorkflowHandler;
use Uzink\BackendBundle\Manager\ArticleManager;
use Uzink\BackendBundle\Manager\CategoryManager;
use Uzink\BackendBundle\Manager\DraftManager;
use Uzink\BackendBundle\Search\ArticleSearch;
use Uzink\BackendBundle\Search\Form\SearchType;
use Uzink\BackendBundle\Security\Permission\PermissionMap;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ArticleController extends ServicesAwareController
{
    /**
     * @var ArticleManager
     */
    protected $articleManager;

    /**
     * @var DraftManager
     */
    protected $draftManager;

    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /**
     * @var WorkflowHandler
     */
    protected $workflowHandler;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->articleManager = $this->get('uzink.article.manager');
        $this->draftManager = $this->get('uzink.draft.manager');
        $this->categoryManager = $this->get('uzink.category.manager');
        $this->workflowHandler = $this->get('uzink.workflow.handler');
    }

    // Set Permissions for articles
    public function setPermissionsAction() {

        $em = $this->getDoctrine()->getManager();
        $request = $em->getRepository('BackendBundle:Article')->find(20);
        $mailer = $this->get('uzink.mailer');
        $mailer->sendNewAssigmentEmail($this->getUser(), $request);
        die("Stop");

        $articles = $this->getDoctrine()->getRepository('BackendBundle:Article')->findAll();
        $articleManager = $this->get('uzink.article.manager');
        foreach ($articles as $article) {
            $articleManager->setPermissions($article);
        }

        $messages = $this->getDoctrine()->getRepository('BackendBundle:Message')->findAll();
        $messageManager = $this->get('uzink.message.manager');
        foreach ($messages as $message) {
            $messageManager->setPermissions($message);
        }

        $categories = $this->getDoctrine()->getRepository('BackendBundle:Category')->findAll();
        $categoryManager = $this->get('uzink.category.manager');
        foreach ($categories as $category) {
            $categoryManager->setPermissions($category);
        }

        $requests = $this->getDoctrine()->getRepository('BackendBundle:Request')->findAll();
        $requestManager = $this->get('uzink.request.manager');
        foreach ($requests as $request) {
            $requestManager->setPermissions($request);
        }        
        
        return $this->redirect($this->generateUrl('workflow.article.list'));
    }

    //<editor-fold desc="Article Basic Actions">
    public function indexAction(Request $request){
        $items = array(
            array('article.assigned')
        );
        $this->makeBreadcrumb($items);

        $articleSearch = new ArticleSearch($this->articleManager, $this->get('security.token_storage'));
        $pagers = $articleSearch->handleRequest($request);

        $categoryId = $request->get('category', null);
        $category = $categoryId?$this->getDoctrine()->getRepository('BackendBundle:Category')->find($categoryId):null;

        return $this->render(
            'FrontBundle:Article:workflow.layout.list.html.twig',
            [
                'assignedArticlesPager'  => $pagers['assignedArticles'],
                'assignedArticlesInRevisionPager'  => $pagers['assignedArticlesInRevision'],
                'assignedArticlesInPublicationPager'  => $pagers['assignedArticlesInPublication'],
                'collaborationsPager'    => $pagers['collaborations'],
                'category'               => $category
            ]
        );
    }

    public function searchAction(Request $request){
        $items = array(
            array('article.search.result')
        );
        $this->makeBreadcrumb($items);

        $options['keyword'] = $request->get('keywords');

        if ($request->get('type') == 'articles') {
            /** @var $repository ArticleRepository */
            $repository = $this->getDoctrine()->getManager()->getRepository('BackendBundle:Article');
            $articles = $repository->searchInternalArticles($options);

            return $this->render(
                'FrontBundle:Article:workflow.layout.search.html.twig',
                [
                    'articles' => $articles
                ]
            );
        } else {
            /** @var $repository CategoryRepository */
            $repository = $this->getDoctrine()->getManager()->getRepository('BackendBundle:Category');
            $categories = $repository->searchInternalCategories($options);

            return $this->render(
                'FrontBundle:Category:workflow.layout.search.html.twig',
                [
                    'categories' => $categories
                ]
            );
        }
    }

    private static function _sortOlderFirst(Article $a, Article $b) {
        if ($a->getCreatedAt() > $b->getCreatedAt()) return -1;
        else if ($a->getCreatedAt() == $b->getCreatedAt()) return 0;
        else return 1;
    }

    public function newArticleAction($id = null, Request $request)
    {
        // Make the breadcrumbs
        $items = array(
            array('article.new')
        );
        $this->makeBreadcrumb($items);

        $category = $this->categoryManager->get($id);
        $article = $this->articleManager->create($category);

        $form = $this->createForm('article', $article);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->articleManager->save($article);

                $draft = $this->articleManager->getDraft($article->getId());
                $this->articleManager->save($draft);

                $this->workflowHandler->startAndSave($article);
                $this->workflowHandler->startAndSave($draft);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('article.actions.created', array(), 'dolopedia')
                );

                return $this->redirect($this->generateUrl('workflow.article.edit', array( 'id' => $article->getId() )));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->get('translator')->trans('article.actions.notValidated', array(), 'dolopedia')
                );
            }
        }

        return $this->render('FrontBundle:Article:workflow.layout.new.html.twig',
            array(
                'form' => $form->createView(),
                'article' => $article
            ));
    }

    /**
     * @Security("is_granted('CONTENT', article)")
     * @param Article $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editArticleAction(Article $article, Request $request) {
        $items = array(
            array('article.assigned', 'workflow.article.list'),
            array($article)
        );
        $this->makeBreadcrumb($items);
    
        $draft = $this->articleManager->getDraft($article);

        $form = $this->createForm('draft', $draft);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $target = $form->get('_target')->getData();
                if (empty($target)) $target = $this->generateUrl('workflow.article.list');

                $flashMessage = null;

                switch (true) {
                    case $form->get('save')->isClicked():
                        $this->draftManager->saveDraft($draft);
                        $flashMessage = 'article.actions.edited';
                        break;

                    case $form->get('save_and_exit')->isClicked():
                        $this->draftManager->saveDraft($draft);
                        return $this->redirect($target);
                        break;

                    case $form->get('revise')->isClicked():
                        $this->draftManager->reviseDraft($draft);
                        $flashMessage = 'article.actions.revision';
                        break;

                    case $form->get('validate')->isClicked():
                        $this->draftManager->validateDraft($draft);
                        $flashMessage = 'article.actions.validated';
                        break;

                    case $form->get('no_validate')->isClicked():
                        $this->draftManager->noValidateDraft($draft);
                        $flashMessage = 'article.actions.not_validated';
                        break;

                    case $form->get('publish')->isClicked():
                        $this->draftManager->publishDraft($draft);
                        $flashMessage = 'article.actions.published';
                        break;

                    case $form->get('no_publish')->isClicked():
                        $this->draftManager->noPublishDraft($draft);
                        $flashMessage = 'article.actions.not_published';
                        break;
                }

                $flashMessage = $this->get('translator')->trans($flashMessage, array(), 'dolopedia');

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $flashMessage
                );
                return $this->redirect($this->generateUrl('workflow.article.edit', array('id' => $article->getId())));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->get('translator')->trans('article.actions.notValidated', array(), 'dolopedia')
                );
            }
        }

        return $this->render('FrontBundle:Article:workflow.layout.edit.html.twig',
            array(
                'form' => $form->createView(),
                'article' => $article
            )
        );
    }

    /**
     * @Security("is_granted('CONTENT', article)")
     * @param Article $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function changeEditorAction(Request $request, Article $article) {
        $user = $this->getUser();
        $rawUsers = $user->getChildrenUsers();

        $users = array();
        /** @var User $userData */
        foreach ($rawUsers as $userData) {
            $users[] = array(
                'id' => $userData->getId(),
                'name' => $userData->getFullName()
            );
        }

        $data = $users;

        if ($request->isMethod('POST')) {
            $editorID = $request->get('newEditor', null);

            if (!$editorID) return new JsonResponse(array('success' => false));

            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('BackendBundle:User');
            $article->setEditor($repo->find($editorID));
            $em->persist($article);
            $em->flush();

            $imageExtension = $this->container->get('uzink.twig.image_extension');
            $image = $imageExtension->userHandlerFilter($article->getEditor()->getImage(), 'user_thumb_pico');
            $translator = $this->container->get('translator');
            $title = 'Supervisor: ' . $article->getEditor()->getFullName() . ' (' . $translator->trans($article->getEditor()->getRole(), array(), 'dolopedia') . ')';

            $data = array(
                'success' => true,
                'image' => $image,
                'title' => $title
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @Security("is_granted('CONTENT', article)")
     * @param Request $request
     * @param Article $article
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCollaboratorAction(Request $request, Article $article, $userID) {
        $em = $this->getDoctrine()->getManager();

        $collaborator = $em->getRepository('BackendBundle:User')->find($userID);
        if ($collaborator) {
            $article->removeCollaborator($collaborator);
            $em->persist($article);
            $em->flush();
        }

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    public function deleteArticleAction(Request $request, $id) {
        $articleManager = $this->get('uzink.article.manager');
        $aclManager = $this->get('uzink.acl.manager');
        $article = $articleManager->get($id);

        if (false === $this->get('security.authorization_checker')->isGranted(PermissionMap::PERMISSION_DELETE, $article)) {
            throw $this->createAccessDeniedException('Access denied!');
        }

        try {
            $user = $this->getUser();
            $event = new ActivityEvent($article, Activity::TYPE_ARTICLE_REMOVE, $user);
            $this->get('event_dispatcher')->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

            $aclManager->clean($article);
            $articleManager->delete($article);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(true);
            } else {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('article.actions.deleteSuccess', array(), 'dolopedia')
                );
            }
        } catch (\Exception $e) {
            $articleManager->setPermissions($article);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(false, 'messagge' => $e->getMessage()));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->get('translator')->trans('article.actions.deleteError', array(), 'dolopedia')
                );
            }
        }

        return $this->redirect($this->generateUrl('workflow.article.list'));
    }
    //</editor-fold>

    //<editor-fold desc="Public Actions">
    /**
     * @param \Uzink\BackendBundle\Entity\Article $article
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @internal param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("article", options={"mapping": {"slug": "seoSlug"}})
     */
    public function showArticleAction(Article $article) {
        $this->makeBreadcrumb($article);

        return $this->render('FrontBundle:Article:public.layout.show.html.twig', array('article' => $article));
    }

    /**
     * @param Article $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function previewArticleAction(Article $article, Request $request) {
        foreach($article->getBibliographicEntries() as $bEntry) {
            $article->removeBibliographicEntry($bEntry);
        }
        $draft = $this->articleManager->getDraft($article);

        $form = $this->createForm('draft', $draft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->fillFromDraft($draft);
            return $this->render('FrontBundle:Article:public.layout.show.html.twig', array('article' => $article));
        }

        return $this->render('FrontBundle:Article:public.layout.show.html.twig', array('article' => $article));
    }
    //</editor-fold>

    //<editor-fold desc="Article Search Actions">
/*    public function searchArticleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $articleRepo = $em->getRepository('BackendBundle:Article');
        $categoryRepo = $em->getRepository('BackendBundle:Category');

        $articleFinder = $this->container->get('fos_elastica.finder.app.article');
        $categoryFinder = $this->container->get('fos_elastica.finder.app.category');

        $items = array(
            array('article.search.title')
        );
        $this->makeBreadcrumb($items);

        $keyword = $request->get('keyword', '');
        $engineId = $request->get('engine', null);
        $categoryId = $request->get('category', null);
        $author = $request->get('author', '');

        $form = $this->createForm(new \Uzink\BackendBundle\Form\SearchType());

        $engineRepo = $this->getDoctrine()->getRepository('BackendBundle:Engine');
        $engine = ($engineId)?$engineRepo->find($engineId):null;

        if (!$engine) {
            $category = ($categoryId)?$categoryRepo->find($categoryId):null;

            $form->get('keyword')->setData($keyword);
            $form->get('engine')->setData($engine);
            $form->get('category')->setData($category);
            $form->get('author')->setData($author);

            $searchableString = '';
            if (!empty($keyword)) $searchableString = $keyword . ' ';
            if (!empty($author)) $searchableString = $author . ' ';
            if ($category) $searchableString = $category->getTitle() . ' ';

            $articles = $articleFinder->find($searchableString);
            $categories = $categoryFinder->find($searchableString);

            return $this->render(
                'FrontBundle:Article:public.layout.search.result.html.twig',
                array(
                    'form'      => $form->createView(),
                    'articles'  => $articles,
                    'categories' => $categories
                )
            );
        }

        $performedUrl = str_replace('_keyword_', $keyword, $engine->getUrl());
        return $this->redirect($performedUrl);
    }*/

    public function searchArticleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $articleRepo = $em->getRepository('BackendBundle:Article');
        $categoryRepo = $em->getRepository('BackendBundle:Category');

        $articleFinder = $this->container->get('fos_elastica.finder.app.article');
        $categoryFinder = $this->container->get('fos_elastica.finder.app.category');

        $items = array(
            array('article.search.title')
        );
        $this->makeBreadcrumb($items);

        $keyword = $request->get('keyword', '');
        $engineId = $request->get('engine', null);
        $categoryId = $request->get('category', null);
        $author = $request->get('author', '');

        $form = $this->createForm(new \Uzink\BackendBundle\Form\SearchType());

        $engineRepo = $this->getDoctrine()->getRepository('BackendBundle:Engine');
        $engine = ($engineId)?$engineRepo->find($engineId):null;

        if (!$engine) {
            $category = ($categoryId)?$categoryRepo->find($categoryId):null;

            $form->get('keyword')->setData($keyword);
            $form->get('engine')->setData($engine);
            $form->get('category')->setData($category);
            $form->get('author')->setData($author);

            $searchableString = '';
            if (!empty($keyword)) $searchableString = $keyword . ' ';
            if (!empty($author)) $searchableString = $author . ' ';
            if ($category) $searchableString = $category->getTitle() . ' ';

            $query = Query::create('*' . $searchableString . '*');
            $articles = $articleFinder->find($query);
            $categories = $categoryFinder->find($query);

            return $this->render(
                'FrontBundle:Article:public.layout.search.result.html.twig',
                array(
                    'form'      => $form->createView(),
                    'articles'  => $articles,
                    'categories' => $categories
                )
            );
        }

        $performedUrl = str_replace('_keyword_', $keyword, $engine->getUrl());
        return $this->redirect($performedUrl);
    }

    public function renderSearchBoxAction()
    {
        $form = $this->createForm(new SearchCondensedType());

        return $this->render(
            'FrontBundle:Component:partial.search.box.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    public function renderResponsiveSearchBoxAction()
    {
        $form = $this->createForm(new SearchCondensedType());

        return $this->render(
            'FrontBundle:Component:partial.search.box.responsive.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
    //</editor-fold>

    //<editor-fold desc="Article Rating Action">
    public function addRatingAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BackendBundle:Rating');
        $articleManager = $this->get('uzink.article.manager');
        $article = $articleManager->get($id);

        $rate = $request->get('rate');

        $criteria = array(
            'owner' => $this->getUser(),
            'article' => $article
        );
        $rating = $repository->findOneBy($criteria);

        if (!$rating) {
            $rating = new Rating();
            $rating->setArticle($article);
            $rating->setOwner($this->getUser());
        }

        $rating->setRating($rate);

        $em->persist($rating);
        $em->flush();

        $article->addRating($rating);
        $articleManager->save($article);

        return new JsonResponse($article->getRate());
    }
    //</editor-fold>

    //<editor-fold desc="Favourite Article Actions">
    public function indexArticlesFavouritesAction(Request $request) {
        $userHandler = $this->get('uzink.user.handler');

        $items = array(
            array('user.favouriteArticles'),
        );
        $userHandler->makeBreadcrumb($items);

        $user = $this->getUser();
        $articles = $user->getFavouritesArticles()->toArray();

        $page = $request->get('page', 1);
        $adapter = new ArrayAdapter($articles);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('pager_per_page'));
        $pager->setCurrentPage($page);

        return $this->render(
            'FrontBundle:Article:panel.layout.favouritesArticles.index.html.twig',
            [
                'pager' => $pager
            ]
        );
    }

    public function addArticleFavouriteAction($id) {
        $user = $this->getUser();
        $userManager = $this->get('uzink.user.manager');
        $articleManager = $this->get('uzink.article.manager');
        $article = $articleManager->get($id);
        $dispatcher = $this->get('event_dispatcher');

        if ($user->isFavouriteArticle($article)) {
            $user->removeFavouritesArticle($article);
            $event = new ActivityEvent(
                $article,
                Activity::TYPE_ARTICLE_REMOVE_TO_FAVOURITE,
                $user
            );
            $response = false;
        } else {
            $user->addFavouritesArticle($article);
            $event = new ActivityEvent(
                $article,
                Activity::TYPE_ARTICLE_ADD_TO_FAVOURITE,
                $user
            );
            $response = true;
        }

        $dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);
        $userManager->save($user);

        return new JsonResponse($response);
    }
    //</editor-fold>

    //<editor-fold desc="Article Tree Actions">
    public function treeArticlePositionModifyAction($id, $action) {
        $articleManager = $this->get('uzink.article.manager');
        $repository = $articleManager->getRepository();

        $currentArticle = $articleManager->get($id);
        $currentCategory = $currentArticle->getCategory();
        $articleManager->refreshSortNumbers($currentCategory);
        $currentPosition = $currentArticle->getPosition();

        switch ($action) {
            case 'up':
                $criteria = array(
                    'category' => $currentCategory,
                    'position' => $currentPosition - 1
                );
                break;

            case 'down';
                $criteria = array(
                    'category' => $currentCategory,
                    'position' => $currentPosition + 1
                );
                break;
        }

        if ($criteria) {
            $swingArticle = $repository->findOneBy($criteria);

            if ($swingArticle) {
                $articleManager->swapPositions($currentArticle, $swingArticle);
            }
        }

        return new JsonResponse(true);
    }
    //</editor-fold>

    //<editor-fold desc="Content Structure Actions">
    public function contentAction($article) {
        $articleHandler = $this->get('uzink.article.handler');
        $structure = $articleHandler->makeContentStructure($article);

        return $this->render('FrontBundle:Article:public.partial.content.html.twig', array('structure' => $structure, 'article' => $article));
    }

    private function iterateForm(&$structure, $field) {
        $structure[$field->getName()] = array();
        $structure[$field->getName()]['title'] = $field->getConfig()->getOption('label');
        $structure[$field->getName()]['type'] = $field->getConfig()->getType()->getName();

        if($field->count() > 0) {
            $structure[$field->getName()]['fields'] = array();
            foreach($field as $item) {
                $this->iterateForm($structure[$field->getName()]['fields'], $item);
            }
        }
    }
    //</editor-fold>

    public function bibliographicEntryAction($uid) {
        $repo = $this->getDoctrine()->getManager()->getRepository('BackendBundle:BibliographicEntry');
        $bEntry = $repo->findOneBy(array('uid' => $uid));

        return new JsonResponse(array(
            'uid' => $bEntry->getUid()?:'',
            'title' => $bEntry->getTitle()?:'',
            'author' => $bEntry->getAuthor()?:'',
            'publication' => $bEntry->getPublication()?:'',
            'pages' => $bEntry->getPages()?:'',
            'volume' => $bEntry->getVolume()?:'',
            'year' => $bEntry->getYear()?:'',
            'link' => $bEntry->getLink()?:''
        ));
    }
}
