<?php

namespace Uzink\BackendBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Translation\LoggingTranslator;
use Uzink\BackendBundle\Entity\Article;

class CategoryChronicPainChoicelist extends LazyChoiceList
{
    /**
     * @var array
     */
    private $choices = array();

    /**
     * @var LoggingTranslator;
     */
    private $translator;

    /**
     * CategoryChronicPainChoicelist constructor.
     */
    public function __construct(LoggingTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Loads the choice list
     *
     * Should be implemented by child classes.
     *
     * @return ChoiceListInterface The loaded choice list
     */
    protected function loadChoiceList()
    {
        $categories = Article::getChronicCategories();
        $this->loadCategories($categories);
        return new SimpleChoiceList($this->choices);
    }

    private function loadCategories($categories, $level = 0, $prefix = null) {
        $basename = 'crossCategory.chronic';
        if ($prefix) $prefix .= '_';
        $tabString = str_repeat('&nbsp;', $level * 2);

        foreach ($categories as $key => $category) {
            if (is_array($category)) {
                $this->choices[$prefix . $key] = $tabString . $this->translator->trans($basename . '.' . $key . '.main', array(), 'dolopedia');
                $this->loadCategories($category, $level + 1, $key);
            }
            else {
                $categoryLabel = $this->translator->trans($category, array(), 'dolopedia');
                $this->choices[$prefix . $key] = $tabString . $categoryLabel;
            }
        }
    }
}