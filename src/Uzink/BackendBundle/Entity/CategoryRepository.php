<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository {
    /**
     * AssignedCategories
     *
     * @param User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryAssignedCategories(User $user) {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.owner = :user')
            ->setParameter('user', $user);

        return $qb;
    }

    public function findAssignedCategories(User $user) {
        $results = $this->queryAssignedCategories($user)->getQuery()->getResult();
        return new ArrayCollection($results);
    }

    /**
     * @param $options
     * @return array
     */
    public function searchCategories($options) {
        $rootNode = $this->findOneByTitle('Dolopedia');

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder
            ->where($queryBuilder->expr()->not($queryBuilder->expr()->eq('c', ':rootNode')))
            ->setParameter(':rootNode', $rootNode)
            ->orderBy('c.title', 'asc');

        if ($options['category']) {
            $queryBuilder
                ->andWhere('c = :category')
                ->setParameter(':category', $options['category']);
        }

        if ($options['author']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    'c.owner.name',
                    $queryBuilder->expr()->literal('%'.$options['author'].'%')
                )
            );
        }

        if ($options['keyword']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    'c.title',
                    $queryBuilder->expr()->literal('%'.$options['keyword'].'%')
                )
            );
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findCategoriesByParent(Category $parentCategory) {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where($queryBuilder->expr()->eq('c.parent', ':parentCategory'));
        $queryBuilder->setParameter(':parentCategory', $parentCategory);
        $queryBuilder->orderBy('c.root, c.lft', 'ASC');
        
        return $queryBuilder->getQuery()->getResult();
    }
    
    public function buildCategoryTree(Category $parentRoot = null) {
        $queryBuilder = $this->createQueryBuilder('node')
            ->select('node')
            ->addSelect('owner')
            ->join('node.owner', 'owner')
            ->orderBy('node.root, node.lft', 'ASC')
        ;

        if($parentRoot) {
            $queryBuilder
                ->where('node.root = :node_root')
                ->setParameter(':node_root', $parentRoot->getId());
        }

        $options = array(
            'decorate' => false
        );
        return $this->buildTree($queryBuilder->getQuery()->getArrayResult(), $options);
    }

    /**
     * @param $options
     * @return array
     */
    public function searchInternalCategories($options) {
        $queryBuilder = $this->createQueryBuilder('c');

        if ($options['keyword']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    'c.title',
                    $queryBuilder->expr()->literal('%'.$options['keyword'].'%')
                )
            );
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countArticles($category) {
        $queryBuilder = $this->childrenQueryBuilder($category, false, null, 'ASC', true);

        $queryBuilder->select('node.id');
        $categoryIDs = array_column($queryBuilder->getQuery()->getResult(), 'id');

        $articleManager = $this->getEntityManager()->getRepository('BackendBundle:Article');
        $articleQB = $articleManager->createQueryBuilder('a');

        $result = $articleQB
            ->select('COUNT(a.id) as articles')
            ->where($articleQB->expr()->in('a.category', $categoryIDs))
            ->getQuery()
            ->setMaxResults(1)
            ->getResult(AbstractQuery::HYDRATE_SCALAR);

        return $result[0]['articles'];
    }
}