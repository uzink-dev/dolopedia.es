<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Uzink\BackendBundle\Search\ArticleSearch;

class ArticleRepository extends EntityRepository {
    /**
     * Search Request Query
     *
     * @param ArticleSearch $articleSearch
     * @return array
     */
    public function searchRequest(ArticleSearch $articleSearch) {
        // Working Variables
        $user = $articleSearch->getUser();
        $sort = 'a.' . $articleSearch->getSort();
        $direction = $articleSearch->getDirection();

        // Creating the query
        $qb = $this->queryAssignedArticles($user);
        $qb->addOrderBy($sort, $direction);

        return $qb->getQuery()->getResult();
    }

    /**
     * AssignedArticles
     *
     * @param User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryAssignedArticles(User $user) {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.owner = :user')
           ->orWhere('a.supervisor = :user')
           ->orWhere('a.editor = :user')
           ->setParameter('user', $user);

        return $qb;
    }

    /**
     * AssignedArticles
     *
     * @return QueryBuilder
     */
    public function queryUnassignedArticles(User $user) {
        $qb = $this->createQueryBuilder('a');

        $qb->andWhere($qb->expr()->eq('a.owner', ':user'))
           ->andWhere($qb->expr()->isNull('a.editor'))
           ->setParameter('user', $user);

        return $qb;
    }

    public function findAssignedArticlesForEdition(User $user)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->orWhere('a.editor = :user')
           ->setParameter('user', $user);

        $results = $qb->getQuery()->getResult();
        return new ArrayCollection($results);
    }

    public function findAssignedArticles(User $user) {
        $results = $this->queryAssignedArticles($user)->getQuery()->getResult();
        return new ArrayCollection($results);
    }

    public function findAssignedArticlesWithStatus(User $user, $status) {
        $qb = $this->queryAssignedArticles($user);

        $qb->innerJoin('a.drafts', 'd', 'WITH', $qb->expr()->like('d.status', ':status'));
        $qb->setParameter('status', $status);
        $results = $qb->getQuery()->getResult();

        $aux = new ArrayCollection();

        foreach($results as $article) {
            /** @var Article $article */
            $drafts = $article->getDrafts();
            /** @var Draft $lastDraft */
            /** @var Draft $draft */
            $lastDraft = null;
            foreach ($drafts as $draft) {
                if (!$lastDraft || $lastDraft->getId() < $draft->getId()) $lastDraft = $draft;
            }

            if ($lastDraft && $lastDraft->getStatus() == $status) $aux->add($article);
        }

        return $aux;
    }

    public function findAssignedArticlesInRevision(User $user) {
        $qb = $this->queryAssignedArticles($user);

        $qb->innerJoin('a.drafts', 'd', 'WITH', $qb->expr()->like('d.status', ':status'));
        $qb->setParameter('status', 'revision');
        $results = $qb->getQuery()->getResult();

        $aux = new ArrayCollection();

        foreach($results as $article) {
            /** @var Article $article */
            $drafts = $article->getDrafts();
            /** @var Draft $lastDraft */
            /** @var Draft $draft */
            $lastDraft = null;
            foreach ($drafts as $draft) {
                if (!$lastDraft || $lastDraft->getId() < $draft->getId()) $lastDraft = $draft;
            }

            if ($lastDraft && $lastDraft->getStatus() == 'revision') $aux->add($article);
        }

        return $aux;
    }

    public function findAssignedArticlesInPublication(User $user) {
        $qb = $this->queryAssignedArticles($user);

        $qb->innerJoin('a.drafts', 'd', 'WITH', $qb->expr()->like('d.status', ':status'));
        $qb->setParameter('status', 'validated');
        $results = $qb->getQuery()->getResult();

        return new ArrayCollection($results);
    }

    /**
     * Base Query Drafts from Article
     *
     * @param Article $article
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryDrafts(Article $article) {
        $repo = $this->getEntityManager()->getRepository('BackendBundle:Draft');
        $qb = $repo->createQueryBuilder('d');
        $qb->where('d.article = :article')
           ->setParameter('article', $article)
           ->addOrderBy('d.createdAt', 'desc')
           ->addOrderBy('d.updatedAt', 'desc');

        return $qb;
    }

    /**
     * Find last Draft from an Article
     *
     * @param Article $article
     * @return mixed
     */
    public function findLastDraft(Article $article) {
        $query = $this->queryDrafts($article)
                      ->setMaxResults(1)
                      ->getQuery();
        return $query->getOneOrNullResult();
    }

    public function findInvolvedArticles(User $user) {
        $em = $this->getEntityManager();
        $qbRequestCreation = $em->createQueryBuilder();
        $qbRequestCreation
            ->select('r')
            ->from('BackendBundle:Request', 'cr')
            ->where('cr.userFrom = :user')
            ->andWhere(
                $qbRequestCreation->expr()->andX(
                    'cr.type = ' . Request::TYPE_REQUEST_NEW,
                    'cr.status = ' . Request::STATUS_REQUEST_ACCEPTED
                )
            );

        $qbArticlesRequestCreation = $em->createQueryBuilder();
        $qbArticlesRequestCreation
            ->select('IDENTITY(acr.article)')
            ->from('BackendBundle:Request', 'acr')
            ->where(
                $qbRequestCreation->expr()->andX(
                    'cr.type = ' . Request::TYPE_EDITION_CREATION,
                    'cr.status = ' . Request::STATUS_EDITION_CREATED
                )
            )
            ->andWhere(
                $qbRequestCreation->expr()->in('cr.previousRequest', $qbRequestCreation)
            );


        // Articles which have a draft created by the user
        $qbDraft = $em->createQueryBuilder();
        $qbDraft
            ->select('IDENTITY(d.article)')
            ->from('BackendBundle:Draft', 'd')
            ->where('d.article IS NOT NULL')
            ->andWhere('d.createdAt = :user');

        // Published articles where the user had been supervisor, editor, owner or has been created by him
        $qbArticles = $em->createQueryBuilder();
        $qbArticles
            ->select('a')
            ->from('BackendBundle:Article', 'a')
            ->where('a.published = true')
            ->andWhere(
                $qbArticles
                    ->expr()
                    ->orX(
                        'a.createdBy = :user',
                        'a.owner = :user',
                        'a.supervisor = :user',
                        'a.editor = :user'
                    )
            );

        $qb = $this->createQueryBuilder('fa');
        $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->in('fa', $qbArticles->getDQL()),
                    $qb->expr()->in('fa', $qbDraft->getDQL()),
                    $qb->expr()->in('fa', $qbArticlesRequestCreation->getDQL())
                )
            )
            ->orderBy('fa.createdAt', 'asc')
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find the published articles
     *
     * @param Category|ArrayCollection $categories
     * @return QueryBuilder
     */
    public function QBFindPublishedArticles($categories = null) {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->orderBy('a.position', 'asc');

        if ($categories && $categories instanceof Category) {
            $queryBuilder
                ->andWhere('a.category = :category')
                ->setParameter(':category', $categories);
        } elseif($categories && $categories instanceof ArrayCollection && $categories->count() > 0) {
            $catOr = $queryBuilder->expr()->orX();
            foreach ($categories as $key => $category) {
                $catOr->add('a.category = :' . $key);
                $queryBuilder->setParameter($key, $category);
            }
            $queryBuilder->andWhere($catOr);
        }

        return $queryBuilder;
    }

    /**
     * @param Category|ArrayCollection $categories
     * @return ArrayCollection
     */
    public function findPublishedArticles($categories = null) {
        return $this->QBFindPublishedArticles($categories)->getQuery()->getResult();
    }

    /**
     * @param $options
     * @return ArrayCollection
     */
    public function searchArticles($options) {
        $queryBuilder = $this->QBFindPublishedArticles($options['category']);
        $queryBuilder->innerJoin('a.editor', 'editor');

        if ($options['author']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    'editor.name',
                    $queryBuilder->expr()->literal('%'.$options['author'].'%')
                )
            );
        }

        if ($options['keyword']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    'a.title',
                    $queryBuilder->expr()->literal('%'.$options['keyword'].'%')
                )
            );
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $options
     * @return ArrayCollection
     */
    public function searchInternalArticles($options) {
        $queryBuilder = $this->createQueryBuilder('a');

        if ($options['keyword']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    'a.title',
                    $queryBuilder->expr()->literal('%'.$options['keyword'].'%')
                )
            );
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function recentArticles() {
        $queryBuilder = $this->QBFindPublishedArticles();
        $queryBuilder
            ->orderBy('a.publishedAt', 'DESC')
            ->setMaxResults(4);

        return $queryBuilder->getQuery()->getResult();
    }

    public function bestRatedArticles() {
        $queryBuilder = $this->QBFindPublishedArticles();
        $queryBuilder
            ->addSelect('AVG(r.rating) AS HIDDEN rate')
            ->leftJoin('a.ratings', 'r')
            ->resetDQLPart('orderBy')
            ->addOrderBy('rate', 'DESC')
            ->addOrderBy('a.title', 'ASC')
            ->groupBy('a.id')
            ->setMaxResults(4);

        return $queryBuilder->getQuery()->getResult();
    }
}