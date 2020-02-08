<?php

namespace Uzink\BackendBundle\Search;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;

interface SearchInterface {
    /**
     * @param Request $request
     * @return mixed
     */
    public function handleRequest(Request $request);

    /**
     * @return array
     */
    public function getPager();

    /**
     * @param null|string $filter
     * @return FormView
     */
    public function getFilters($filter = null);
} 