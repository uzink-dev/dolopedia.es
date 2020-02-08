<?php

namespace Uzink\BackendBundle\Controller;

use Doctrine\ORM\EntityManager;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Pagerfanta;
use Sortable\Fixture\Document\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\BaseArticle;
use Uzink\BackendBundle\Entity\Category;
use Uzink\BackendBundle\Entity\CategoryRepository;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Event\ActivityEvent;
use Uzink\BackendBundle\Form\CategoryType;
use Uzink\BackendBundle\Manager\CategoryManager;
use Uzink\BackendBundle\Security\Permission\PermissionMap;

class CategoryController extends Controller
{
    private $em;
    /**
     * @var CategoryRepository
     */
    private $repository;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        
        $this->em = $this->getDoctrine()->getManager();
        $this->repository = $this->em->getRepository('BackendBundle:Category');        
    }

    public function showCategoryAction($slug) {
        $category = $this->repository->findOneBySeoSlug($slug);
        $articles = $this->em->getRepository('BackendBundle:Article')->findPublishedArticles($category);
        if (!$category)
            throw $this->createNotFoundException('No existe la categoría solicitada');

        $this->get('uzink.category.handler')->makeBreadcrumb($category);

        return $this->render('FrontBundle:Category:public.layout.show.html.twig',
             array(
                 'category' => $category,
                 'articles' => $articles
             ));
    }

    public function indexAction(Request $request){
        $page = $request->get('page', 1);

        $items = array(
            array('category.assigned')
        );
        $this->get('uzink.user.handler')->makeBreadcrumb($items);

        /** @var CategoryRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository('BackendBundle:Category');
        $categories = $repo->findAssignedCategories($this->getUser());

        $itemsPerPage = $this->container->getParameter('pager_per_page');
        $pagerAdapter = new DoctrineCollectionAdapter($categories);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setMaxPerPage($itemsPerPage);
        $pager->setCurrentPage($page);

        return $this->render(
            'FrontBundle:Category:workflow.layout.list.html.twig',
            [
                'categoriesPager'  => $pager
            ]
        );
    }

    public function ListCategoriesAction() {
        $categories = $this
            ->repository
            ->findOneByTitle('Dolopedia')
            ->getChildren();

        // Make the breadcrumbs
        $items = array(
            array($this->get('translator')->trans('article.articles', array(), 'dolopedia'))
        );
        $this->get('uzink.user.handler')->makeBreadcrumb($items);

        return $this->render('FrontBundle:Category:public.layout.mainList.html.twig', array('categories' => $categories));
    }

    public function gridCategoriesAction(Category $parentCategory = null) {
        if (!$parentCategory) {
            $parentCategory = $this->repository->findOneBy(array('title' => 'Dolopedia'));
        }

        $categories = $this->repository->findCategoriesByParent($parentCategory);

        $categoryRepository = $this->getDoctrine()->getManager()->getRepository('BackendBundle:Category');
        foreach ($categories as $category) {
            $category->articlesNB = $categoryRepository->countArticles($category);
        }

        return $this->render('FrontBundle:Category:public.partial.grid.html.twig', array('categories' => $categories));
    }

    // Workflow Actions
    public function treeCategoryAction(Request $request) {
        $popup = $request->get('popup', false);
        $showArticles = $request->get('showArticles', true);
        $rootCategory = $this->repository->findOneByTitle('Dolopedia');
        $categories = $this->repository->buildCategoryTree($rootCategory);

        $articleRepo = $this->em->getRepository('BackendBundle:Article');
        $articles = $articleRepo->findBy(array(), ['category' => 'ASC', 'position' => 'ASC']);

        $categorizedArticles = $this->categorizeArticles($articles);
        $this->fillArticles($categories, $categorizedArticles);

        if (!$popup) {
            // Make the breadcrumbs
            $items = array(
                array('category.tree')
            );
            $this->get('uzink.user.handler')->makeBreadcrumb($items);

            return $this->render('FrontBundle:Category:workflow.layout.tree.html.twig', array('categories' => $categories, 'show_articles' => $showArticles, 'popup' => false));
        } else {
            return $this->render('FrontBundle:Category:workflow.partial.tree.html.twig', array('categories' => $categories, 'show_articles' => $showArticles, 'popup' => true));
        }
    }

    public function categorizeArticles($articles) {
        $categorizedArticles = array();
        foreach ($articles as $article) {
            $category = $article->getCategory();
            if ($category) {
                $categoryId = $category->getId();
                if (!array_key_exists($categoryId, $categorizedArticles)) {
                    $categorizedArticles[$categoryId] = array();
                }
                $categorizedArticles[$categoryId][] = $article;
            }

        }

        return $categorizedArticles;
    }

    public function fillArticles(&$categories, $categorizedArticles) {
        foreach ($categories as &$category) {
            $categoryId = $category['id'];
            $articles = array();
            if (array_key_exists($categoryId, $categorizedArticles))
                $articles = $categorizedArticles[$categoryId];

            $category['articles'] = $articles;

            if (count($category['__children']) > 0)
                $this->fillArticles($category['__children'], $categorizedArticles);

        }
    }
    
    public function treeCategoryPositionModifyAction($id, $action) {
        $categoryManager = $this->get('uzink.category.manager');
        $repository = $categoryManager->getRepository();
        
        $currentCategory = $categoryManager->get($id);

        switch ($action) {
            case 'up':
                $repository->moveUp($currentCategory, 1);
                break;

            case 'down';
                $repository->moveDown($currentCategory, 1);
                break;
        }

        return new JsonResponse(true);
    }
    
    public function categoryNewAction(Request $request, $id = null) {
        $categoryManager = $this->get('uzink.category.manager');   
        
        // Make the breadcrumbs
        $items = array(
            array('category.tree', 'workflow.category.tree'),
            array('category.new')
        );
        $this->get('uzink.user.handler')->makeBreadcrumb($items);
        
        $category = $categoryManager->create();

        $parentCategory = null;
        if ($id) {
            try {
                $parentCategory = $categoryManager->get($id);
                $category->setParent($parentCategory);
            } catch(\Exception $e) {
                $this->get('logger')->info("Don't found parent category when trying to create a new category");
            }
        }

        if ($parentCategory) {
            if (false === $this->get('security.authorization_checker')->isGranted(PermissionMap::PERMISSION_OWNER, $parentCategory)) {
                throw $this->createAccessDeniedException('Access denied!');
            }
        } else {
            if (false === $this->get('security.authorization_checker')->isGranted(User::ROLE_ADMIN)) {
                throw $this->createAccessDeniedException('Access denied!');
            }
        }

        // Make and handle the form
        $form = $this->createForm(new CategoryType(), $category);
        
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $categoryManager->save($category);

                $event = new ActivityEvent($category, Activity::TYPE_CATEGORY_NEW, $this->getUser(), $this->getUser());
                $this->get('event_dispatcher')->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('category.actions.created', array(), 'dolopedia')
                );
                
                return $this->redirect($this->generateUrl('workflow.category.edit', array('id' => $category->getId())));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->get('translator')->trans('category.actions.notValidated', array(), 'dolopedia')
                );
            }
        }
        
        return $this->render('FrontBundle:Category:workflow.layout.new.html.twig', array('form' => $form->createView()));        
    }

    public function categoryCloneAction(Request $request, $id = null) {
        $categoryManager = $this->get('uzink.category.manager');

        // Make the breadcrumbs
        $items = array(
            array('category.tree', 'workflow.category.tree'),
            array('category.new')
        );
        $this->get('uzink.user.handler')->makeBreadcrumb($items);

        /** @var Category $masterCategory */
        $masterCategory = $categoryManager->get($id);
        $newCategory = new Category($masterCategory);
        $newCategory->setParent($masterCategory->getParent());
        $newCategory->setTitle($newCategory->getTitle() . ' (Copia)');

        $categoryManager->save($newCategory);

        return $this->redirect($request->headers->get('referer'));
    }

    public function categoryEditAction(Request $request, $id) {
        /** @var CategoryManager $categoryManager */
        $categoryManager = $this->get('uzink.category.manager');   
        
        $category = $categoryManager->get($id);

        if (false === $this->get('security.authorization_checker')->isGranted(PermissionMap::PERMISSION_OWNER, $category)) {
            throw $this->createAccessDeniedException('Access denied!');
        }

        // Make the breadcrumbs
        $items = array(
            array('category.tree', 'workflow.category.tree'),
            array('category.edit')
        );
        $this->get('uzink.user.handler')->makeBreadcrumb($items);
        
        // Make and handle the form
        $form = $this->createForm(new CategoryType($id), $category);
        
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $category = $categoryManager->update($category);
                $form = $this->createForm(new CategoryType($id), $category);

                $categoryManager->update($category);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('category.actions.edited', array(), 'dolopedia')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->get('translator')->trans('category.actions.notValidated', array(), 'dolopedia')
                );
            }
        }
        
        return $this->render('FrontBundle:Category:workflow.layout.edit.html.twig', array('form' => $form->createView()));        
    }
    
    public function categoryDeleteAction($id) {
        $categoryManager = $this->get('uzink.category.manager');   
        
        $category = $categoryManager->get($id);

        if (false === $this->get('security.authorization_checker')->isGranted(PermissionMap::PERMISSION_OWNER, $category)) {
            throw $this->createAccessDeniedException('Access denied!');
        }

        $childrenCount = $category->getChildren()->count();
        $articleCount = $category->getArticles()->count();
        
        if ($childrenCount > 0 || $articleCount > 0) {
            return new JsonResponse(false);
        }

        $event = new ActivityEvent($category, Activity::TYPE_CATEGORY_DELETE, $this->getUser(), $this->getUser());
        $this->get('event_dispatcher')->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

        $categoryManager->delete($category);
        
        return new JsonResponse(true);
    }    
}
