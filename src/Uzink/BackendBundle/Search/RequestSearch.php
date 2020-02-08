<?php

namespace Uzink\BackendBundle\Search;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Uzink\BackendBundle\Entity\User;

class RequestSearch
{
    const PAGINATION_SENT = 'envpage';
    const PAGINATION_RECEIVED = 'recpage';

    const FILTER_SELECTOR = 'sortSelect';
    const FILTER_PROPERTY = 'sort';
    const FILTER_DIRECTION = 'direction';

    // a public array to be used as choices list in the form
    public static $sortChoices = array(
        'createdAt desc' => 'request.filter.newerFirst',
        'createdAt asc' => 'request.filter.olderFirst',
    );

    // define the field use for the sorting
    protected $sort = 'createdAt';

    // define the sort order
    protected $direction = 'desc';

    // a "virtual" property to add a select tag
    protected $sortSelect;

    // the page number
    protected $pageReceived = 1;
    protected $pageSent = 1;

    // the number of items per page
    protected $perPage = 10;

    protected $user;
    protected $form;

    public function __construct(User $user, Form $form)
    {
        $this->user = $user;
        $this->form = $form;
        $this->initSortSelect();
    }

    // other getters and setters

    public function handleRequest(Request $request)
    {
        $this->setPageReceived($request->get(self::PAGINATION_RECEIVED, 1));
        $this->setPageSent($request->get(self::PAGINATION_SENT, 1));
        $this->form->handleRequest($request);
        $this->setSort($this->form->get(self::FILTER_PROPERTY)->getData());
        $this->setDirection($this->form->get(self::FILTER_DIRECTION, $this->direction)->getData());
    }

    public function getPageReceived()
    {
        return $this->pageReceived;
    }

    public function getPageSent()
    {
        return $this->pageSent;
    }

    public function setPageReceived($page)
    {
        if ($page != null) {
            $this->pageReceived = $page;
        }

        return $this;
    }

    public function setPageSent($page)
    {
        if ($page != null) {
            $this->pageSent = $page;
        }

        return $this;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function setPerPage($perPage=null)
    {
        if($perPage != null){
            $this->perPage = $perPage;
        }

        return $this;
    }

    public function setSortSelect($sortSelect)
    {
        if ($sortSelect != null) {
            $this->sortSelect =  $sortSelect;
        }
    }

    public function getSortSelect()
    {
        return $this->sort.' '.$this->direction;
    }

    public function initSortSelect()
    {
        $this->sortSelect = $this->sort.' '.$this->direction;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        if ($sort != null) {
            $this->sort = $sort;
            $this->initSortSelect();
        }

        return $this;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setDirection($direction)
    {
        if ($direction != null) {
            $this->direction = $direction;
            $this->initSortSelect();
        }

        return $this;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user;
    }

    public function setForm(Form $form) {
        $this->form = $form;
    }

    public function getForm() {
        return $this->form;
    }    
}