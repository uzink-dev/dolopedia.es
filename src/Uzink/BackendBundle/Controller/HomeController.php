<?php

namespace Uzink\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Uzink\BackendBundle\Entity\Article;

class HomeController extends Controller
{
    public function indexAction() {
        return $this->render('FrontBundle:Home:public.layout.home.html.twig', array());
    }

    public function promotersAction() {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BackendBundle:Promoter');
        $promoters = $repo->findAll();

        return $this->render('FrontBundle:Home:public.partial.promoter.html.twig', array('promoters' => $promoters));
    }

    public function featuredAction() {
        $recentArticles = null;
        $bestRatedArticles = null;
        $moreFollowed = null;

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BackendBundle:CategoryLink');

        $sharpCategories = array();
        $chronicCategories = array();

        foreach (Article::getSharpCategories() as $key => $label) {
            $data = array();
            $data['label'] = $label;
            $data['categorySlug'] = null;
            $sharpCategories[$key] = $data;
        }

        $categories = $repo->findCategoryLinksBySlugs(array_keys(Article::getSharpCategories()));
        foreach ($categories as $category) {
            $sharpCategories[$category['slug']]['categorySlug'] = $category['seoSlug'];
        }

        $categorySlugs = array();
        foreach (Article::getChronicCategories() as $cKey => $category) {
            foreach ($category as $iKey => $label) {
                $key = $cKey . '_' . $iKey;
                $categorySlugs[] = $key;
            }
        }

        $categories = $repo->findCategoryLinksBySlugs($categorySlugs);
        $categoriesBySlug = array();
        foreach ($categories as $category) {
            $categoriesBySlug[$category['slug']] = $category['seoSlug'];
        }
        foreach (Article::getChronicCategories() as $cKey => $category) {
            $cData = array();
            $cData['label'] = 'crossCategory.chronic.' . $cKey . '.main';
            $items = array();
            foreach ($category as $iKey => $label) {
                $key = $cKey . '_' . $iKey;

                $iData = array();
                $iData['label'] = $label;
                $iData['categorySlug'] = null;
                if (array_key_exists($key, $categoriesBySlug)) $iData['categorySlug'] = $categoriesBySlug[$key];

                $items[$iKey] = $iData;
            }
            $cData['items'] = $items;
            $chronicCategories[$cKey] = $cData;
        }

        return $this->render(
            'FrontBundle:Home:public.partial.featured.html.twig',
            [
                'sharpCategories' => $sharpCategories,
                'chronicCategories' => $chronicCategories
            ]
        );
    }
}
