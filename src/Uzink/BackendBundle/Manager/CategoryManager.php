<?php
namespace Uzink\BackendBundle\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Uzink\BackendBundle\Entity\BibliographicEntry;
use Uzink\BackendBundle\Entity\Category;
use Uzink\BackendBundle\Entity\CategoryRepository;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Security\Permission\PermissionMap;

class CategoryManager extends Manager
{
    private $categories;

    /** @var array */
    private $bEntries = array();

    /** @var int */
    private $position = 1;

    public function getByParent($parentCategory = null) {
        $criteria = (!$parentCategory)?array('parentCategory' => null):array('parentCategory' => $parentCategory);
        $orderBy = array('position' => 'ASC', 'id' => 'ASC');
        
        $categories = $this->repo->findBy($criteria, $orderBy);
        return $categories;
    }

    /**
     * @param \Uzink\BackendBundle\Entity\Category $entity
     * @return \Uzink\BackendBundle\Entity\Category
     */
    public function save(&$entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        $rootNode = $this->repo->findBy(['title' => 'Dolopedia']);
        $this->updateNodes($rootNode);
        $this->em->flush();

        $this->repo->recover();
        $this->em->flush();

        $this->refreshBibliographicEntries($entity);

        return $entity;
    }

    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        $rootNode = $this->repo->findBy(['title' => 'Dolopedia']);
        $this->updateNodes($rootNode);
        $this->em->flush();

        $this->repo->recover();
        $this->em->flush();

        $this->refreshBibliographicEntries($entity);

        return $entity;
    }

    public function copy(Category $category)
    {
        $newCategory = clone $category;

        $this->setParents($newCategory);
        $newCategory->setTitle($newCategory->getTitle() . '(Copia)');

        return $newCategory;
    }

    private function setParents(Category &$category)
    {
        foreach($category->getChildren() as $child) {
            $child->setParent($category);
            $this->setParents($category);
        }
    }

    private function updateNodes($nodes, $level = 0) {
        foreach ($nodes as $node) {
            $node->setLvl($level);
            $nodes = $this->repo->children($node, true);
            $this->em->persist($node);
            $this->updateNodes($nodes, $level +1);
        }
    }

    public function refreshSortNumbers($parentCategory = null) {
        $categories = $this->getCategoriesByParent($parentCategory);
        $this->orderCategories($categories);

        $this->em->flush();
    }

    public function getCategoriesByParent(Category $parentCategory = null) {
        if (!$this->categories) {
            $categoryRepo = $this->em->getRepository('BackendBundle:Category');
            $this->categories = $categoryRepo->findAll();
        }

        $categoriesCollection = new ArrayCollection();

        foreach($this->categories as $category) {
            if ($category->getParent() == $parentCategory) {
                $categoriesCollection->add($category);
            }
        }

        return $categoriesCollection;
    }



    public function orderCategories($categories) {
        $sortNumber = 1;

        foreach ($categories as $category) {
            $category->setPosition($sortNumber);
            $sortNumber++;
            $this->em->persist($category);
            $this->refreshSortNumbers($category);
        }
    }

    public function refreshBibliographicEntries($entity) {
        /** @var Category $category */
        if (is_int($entity)) $category = $this->get($entity);
        elseif ($entity instanceof Category) $category = $entity;
        else throw new NotFoundHttpException('Resource Not Found');

        $this->position = 1;
        $this->bEntries = array();

        $introduction = $category->getIntroduction();
        $description = $category->getDescription();

        if ($introduction != null || $description != null) {

            if ($introduction != null) {
                $modifiedIntroduction = $this->searchAndReplace($introduction);
                $category->setIntroduction($modifiedIntroduction);
            }

            if ($description != null) {
                $modifiedDescription = $this->searchAndReplace($description);
                $category->setDescription($modifiedDescription);
            }

            $this->cleanBEntries($category);

            parent::save($category);
        }
    }

    private function searchAndReplace($content) {
        $document = new \DOMDocument();
        try {
            $document->loadHTML($content);

            $elements = $document->getElementsByTagName('a');
            foreach($elements as $element) {
                $uid = $element->attributes->getNamedItem('data-bibliography-uid');

                if ($uid) {
                    $uid = $uid->value;
                    $repo = $this->em->getRepository('BackendBundle:BibliographicEntry');
                    /** @var BibliographicEntry $bEntry */
                    $bEntry = $repo->findOneByUid($uid);

                    if ($bEntry) {
                        $this->bEntries[] = $uid;
                        if ($bEntry->getPosition() != $this->position) {
                            $bEntry->setPosition($this->position);
                            $this->em->persist($bEntry);
                            $this->em->flush();
                        }

                        $element->setAttribute('name', 'entry-old');

                        $link = $document->createElement('a', $this->position);
                        $link->setAttribute('data-bibliography-uid', $uid);
                        $link->setAttribute('name', 'entry-' . $uid);
                        $link->setAttribute('href', '#'.$uid);
                        $link->setAttribute('class', 'reference-link');

                        $element->parentNode->replaceChild($link, $element);

                        $this->position++;
                    }
                }
            }

            $html = $document->saveHTML();
            $layout = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);

            return $layout;

        } catch (\Exception $e) {
            // TODO::Handling Exception
        }

        return '';
    }

    private function cleanBEntries(&$category) {
        /** @var Category $category */
        $allBEntries = $category->getBibliographicEntries();

        foreach($allBEntries as $entry) {
            $uid = $entry->getUid();
            if (!in_array($uid, $this->bEntries)) {
                $category->removeBibliographicEntry($entry);
                //$this->em->remove($entry);
            }
        }

        $this->em->flush();
    }

    public function refreshSortNumbers2($parentCategory = null) {
        $sortNumber = 1;
        
        $categories = $this->getByParent($parentCategory);
        
        foreach ($categories as $category) {
            $category->setPosition($sortNumber);
            $sortNumber++;
            $this->em->persist($category);
            $this->em->flush();
            $this->refreshSortNumbers($category);
        }
    }
    
    public function swapPositions($category1, $category2) {
        $p1 = $category1->getPosition();
        $p2 = $category2->getPosition();
        
        $category1->setPosition($p2);
        $category2->setPosition($p1);
        
        $this->em->persist($category1);
        $this->em->persist($category2);
        $this->em->flush();
    }

    /**
     * @param Category $entity
     * @return null|void
     */
    public function setPermissions(Category $entity)
    {
        $aclManager = $this->aclManager;

        $owner = $entity->getOwner();

        $users = array();
        $users[] = $owner;
        $aclManager->clean($entity, $users);

        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), User::ROLE_ADMIN);
        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), $owner);
        $parentCategory = $entity->getParent();
        while ($parentCategory != null) {
            $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), $parentCategory->getOwner());
            $parentCategory = $parentCategory->getParent();
        }
    }

    /**
     * @param Category $entity
     * @param $user
     * @internal param $users
     */
    public function updatePermissions($entity, $user)
    {
        $aclManager = $this->aclManager;

        if ($user) {
            $aclManager->revoke($entity, array(PermissionMap::PERMISSION_OWNER), $user['old']);
            $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), $user['new']);
        }
    }
}