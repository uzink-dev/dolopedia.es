<?php

namespace Uzink\BackendBundle\Search;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Manager\ArticleManager;

class ArticleSearch
{
    const ITEMS_PER_PAGE = 10;

    const PARAM_ASSIGNED_ARTICLES = 'asignados';
    const PARAM_ASSIGNED_ARTICLES_IN_REVISION = 'asignados_en_revision';
    const PARAM_ASSIGNED_ARTICLES_IN_PUBLICATION = 'asignados_en_publicacion';
    const PARAM_COLLABORATIONS = 'colaboraciones';

    /**
     * @var ArticleManager
     */
    private $articleManager;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(ArticleManager $articleManager, TokenStorage $tokenStorage)
    {
        $this->articleManager = $articleManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function handleRequest(Request $request)
    {
        $data = array();
        $types = ['assignedArticles', 'assignedArticlesInRevision', 'assignedArticlesInPublication', 'collaborations'];

        $user = $this->tokenStorage->getToken()->getUser();
        $data['assignedArticles'] = $this->articleManager->getRepository()->findAssignedArticlesForEdition($user);
        $data['assignedArticlesInRevision'] = $this->articleManager->getRepository()->findAssignedArticlesWithStatus($user, 'revision');
        $data['assignedArticlesInPublication'] = $this->articleManager->getRepository()->findAssignedArticlesWithStatus($user, 'validated');
        $data['collaborations'] = $user->getCollaborations();
        foreach ($data['collaborations'] as $collaboration) {
            if ($data['assignedArticles']->contains($collaboration)) $data['collaborations']->removeElement($collaboration);
        }

        $category = $request->get('category', null);
        if ($category) {
            foreach($types as $type) {
                $auxCollection = new ArrayCollection();
                /** @var Article $entity */
                foreach ($data[$type] as $entity) {
                    if ($entity->getCategory() && $entity->getCategory()->getId() == $category)
                        $auxCollection->add($entity);
                }
                $data[$type] = $auxCollection;
            }
        }
        foreach($types as $type) {
            $data[$type] = $data[$type]->toArray();
        }

        $page = array();
        $page['assignedArticles'] = $request->get(self::PARAM_ASSIGNED_ARTICLES, 1);
        $page['assignedArticlesInRevision'] = $request->get(self::PARAM_ASSIGNED_ARTICLES_IN_REVISION, 1);
        $page['assignedArticlesInPublication'] = $request->get(self::PARAM_ASSIGNED_ARTICLES_IN_PUBLICATION, 1);
        $page['collaborations'] = $request->get(self::PARAM_COLLABORATIONS, 1);
        $order = $request->get('order', 'newerFirst');

        if ($order == 'olderFirst') {
            usort($assignedArticles, 'self::_sortOlderFirst');
            usort($collaborations, 'self::_sortOlderFirst');
        }

        $pagers = array();
        foreach($types as $type) {
            $pagers[$type] = $this->articleManager->getPager($data[$type], self::ITEMS_PER_PAGE, $page[$type]);
        }

        return $pagers;
    }

    private static function _sortOlderFirst(Article $a, Article $b) {
        if ($a->getCreatedAt() > $b->getCreatedAt()) return -1;
        else if ($a->getCreatedAt() == $b->getCreatedAt()) return 0;
        else return 1;
    }
}