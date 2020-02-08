<?php

namespace Uzink\BackendBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Uzink\BackendBundle\Entity\Article;

class CategorySharpPainChoicelist extends LazyChoiceList
{
    /**
     * @var array
     */
    private $choices = array();

    /**
     * Loads the choice list
     *
     * Should be implemented by child classes.
     *
     * @return ChoiceListInterface The loaded choice list
     */
    protected function loadChoiceList()
    {
        $categories = Article::getSharpCategories();
        $choices = array();

        foreach ($categories as $key => $category) {
            $choices[$key] = $category;
        }

        $this->choices = $choices;

        return new SimpleChoiceList($this->choices);
    }
}