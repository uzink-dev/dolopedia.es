<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoryLinkRepository extends EntityRepository {
    public function findCategoryLinksBySlugs($slugs) {
        $queryBuilder = $this->createQueryBuilder('cl');

        $queryBuilder
            ->innerJoin('cl.category', 'c')
            ->select(array('cl.slug', 'c.seoSlug'))
            ->where($queryBuilder->expr()->in('cl.slug', $slugs));

        return $queryBuilder
            ->getQuery()
            ->useQueryCache(true)
            ->useQueryCache(true)
            ->getResult();
    }
}