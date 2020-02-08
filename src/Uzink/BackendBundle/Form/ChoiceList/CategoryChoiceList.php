<?php

namespace Uzink\BackendBundle\Form\ChoiceList;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Tree\Fixture\Category;

class CategoryChoiceList extends LazyChoiceList {
    /**
     * @var \Uzink\BackendBundle\Entity\CategoryRepository
     */
    private $categoryRepo;

    /**
     * @var array
     */
    private $choices = array();

    /**
     * @var int
     */
    private $rootVisible;

    /**
     * @var int
     */
    private $invisibleNode;

    public function __construct(EntityManager $em, $rootVisible = false, $invisibleNode = null)
    {
        $this->categoryRepo = $em->getRepository('BackendBundle:Category');
        $this->rootVisible = $rootVisible;
        $this->invisibleNode = $invisibleNode;
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
        if ($this->rootVisible) {
            $tree = $this->categoryRepo->childrenHierarchy();
        } else {
            $rootNode = $this->categoryRepo->findOneBy(['title' => 'Dolopedia']);
            $tree = $this->categoryRepo->childrenHierarchy($rootNode);
        }

        $this->addChoices($tree);

        return new SimpleChoiceList($this->choices);
    }

    private function addChoices($nodes, $level = 0)
    {
        foreach($nodes as $node) {
            $spaces = str_repeat('&nbsp;&nbsp;', $level);
            if (!$this->invisibleNode || $this->invisibleNode != $node['id']) {
                $this->choices[$node['id']] = $spaces . $node['title'];
                if (count($node['__children']) > 0)
                    $this->addChoices($node['__children'], $level + 1);
            }
        }
    }

    public function setInvisibleNode($invisibleNode) {
        $this->invisibleNode = $invisibleNode;
        return $this;
    }
}
