<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Uzink\BackendBundle\Search\RequestSearch;

class RequestRepository extends EntityRepository {
    public function searchRequest(RequestSearch $requestSearch) {
        // Result Variables
        $requests = array(
            'sent' => null,
            'received' => null
        );

        // Working Variables
        $user = $requestSearch->getUser();
        $sort = 'u.' . $requestSearch->getSort();
        $direction = $requestSearch->getDirection();

        // Creating the query
        $qbs = $this->createQueryBuilder('u');
        $qbs->where('u.userFrom = :user')
            ->setParameter('user', $user)
            ->addOrderBy($sort, $direction);

        $qbr = $this->createQueryBuilder('u');
        $qbr->where('u.userTo = :user')
            ->setParameter('user', $user)
            ->addOrderBy($sort, $direction);

        $requests['sent'] = $qbs->getQuery()->getResult();
        $requests['received'] = $qbr->getQuery()->getResult();

        return $requests;
    }
} 