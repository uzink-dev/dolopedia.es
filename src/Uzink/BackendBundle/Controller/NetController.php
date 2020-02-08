<?php

namespace Uzink\BackendBundle\Controller;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NetController extends Controller {
    public function indexAction(Request $request) {
        $userHandler = $this->get('uzink.user.handler');

        $items = array(
            array('net.title')
        );
        $userHandler->makeBreadcrumb($items);

        return $this->render('FrontBundle:Net:public.layout.index.html.twig');
    }

    public function indexCenterAction(Request $request) {
        $userHandler = $this->get('uzink.user.handler');
        $pager = $this->getPager($request, 'BackendBundle:Center');

        $items = array(
            array('net.title', 'public.net.index'),
            array('net.center.title')
        );
        $userHandler->makeBreadcrumb($items);

        return $this->render(
            'FrontBundle:Net:public.layout.center.index.html.twig',
            array(
                'pager' => $pager
            )
        );
    }

    public function showCenterAction(Request $request, $slug) {
        $centerRepo = $this->getDoctrine()->getRepository('BackendBundle:Center');
        $center = $centerRepo->findOneBySeoSlug($slug);
        if (!$center) throw new NotFoundHttpException('Resource Not Found');

        $items = array(
            array('net.title', 'public.net.index'),
            array('net.center.title', 'public.net.center.index'),
            array($center->getTitle())
        );
        $userHandler = $this->get('uzink.user.handler');
        $userHandler->makeBreadcrumb($items);

        return $this->render(
            'FrontBundle:Net:public.layout.center.show.html.twig',
            array(
                'center' => $center
            )
        );
    }

    public function indexPromoterAction(Request $request) {
        $userHandler = $this->get('uzink.user.handler');
        $pager = $this->getPager($request, 'BackendBundle:Promoter');

        $items = array(
            array('net.title', 'public.net.index'),
            array('net.promoter.title')
        );
        $userHandler->makeBreadcrumb($items);

        return $this->render(
            'FrontBundle:Net:public.layout.promoter.index.html.twig',
            array(
                'pager' => $pager
            )
        );
    }

    public function showPromoterAction(Request $request, $slug) {
        $promoterRepo = $this->getDoctrine()->getRepository('BackendBundle:Promoter');
        $promoter = $promoterRepo->findOneBySeoSlug($slug);
        if (!$promoter) throw new NotFoundHttpException('Resource Not Found');

        $items = array(
            array('net.title', 'public.net.index'),
            array('net.promoter.title', 'public.net.promoter.index'),
            array($promoter->getTitle())
        );
        $userHandler = $this->get('uzink.user.handler');
        $userHandler->makeBreadcrumb($items);

        return $this->render(
            'FrontBundle:Net:public.layout.promoter.show.html.twig',
            array(
                'promoter' => $promoter
            )
        );
    }

    private function getPager(Request $request, $repository)
    {
        $page = $request->get('page', 1);
        $filter = $request->get('filter', 'all');

        $repo = $this->getDoctrine()->getRepository($repository);
        $qb = $repo->createQueryBuilder('i');
        switch ($filter) {
            case 'digit':
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('i.title', $qb->expr()->literal('0%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('2%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('3%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('4%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('5%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('6%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('7%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('8%')),
                        $qb->expr()->like('i.title', $qb->expr()->literal('9%'))
                    )
                );
                break;
            case 'all':
                break;
            default:
                $qb->andWhere(
                    $qb->expr()->like('i.title', $qb->expr()->literal($filter.'%'))
                );
        }

        $result = $qb->getQuery()->getResult();

        $adapter = new ArrayAdapter($result);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('pager_per_page_grid'));
        $pager->setCurrentPage($page);

        return $pager;
    }
}