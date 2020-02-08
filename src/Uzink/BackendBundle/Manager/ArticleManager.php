<?php
namespace Uzink\BackendBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Draft;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Security\Permission\PermissionMap;

class ArticleManager extends Manager
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var array
     */
    private $bEntries;

    public function __construct($class, $handler = null)
    {
        $this->class = $class;
        $this->handler = $handler;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->tokenStorage = $this->container->get('security.token_storage');
    }

    public function create($category = null)
    {
        $article = new Article();

        $currentUser = $this->tokenStorage->getToken()->getUser();
        $article->setOwner($currentUser);
        $article->setSupervisor($currentUser);
        $article->setEditor($currentUser);

        if ($category) {
            $article->setCategory($category);
            $article->setSupervisor($category->getOwner());
        }

        return $article;
    }

    public function get($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) $entity = $this->em->getRepository('BackendBundle:Draft')->find($id);
        if (!$entity) throw new NotFoundHttpException('Resource Not Found');
        return $entity;
    }

    public function save(&$entity) {
        if ($entity instanceof Draft) {
            $article = $entity->getArticle();

            if($article->getOwner()) $article->addCollaborators($article->getOwner());
            if($article->getSupervisor()) $article->addCollaborators($article->getSupervisor());
            if($article->getEditor()) $article->addCollaborators($article->getEditor());

            parent::save($article);
        }

        parent::save($entity);

        $this->refreshBibliographicEntries($entity);
    }

    public function getDraft($id) {
        if ($id instanceof Article) $id = $id->getId();
        $article = $this->get($id);
        $draft = $this->repo->findLastDraft($article);
        if (!$draft) $draft = new Draft($article);

        return $draft;
    }

    public function getByCategory($category) {
        $criteria = array('category' => $category);
        $orderBy = array('position' => 'ASC', 'id' => 'ASC');

        $articles = $this->repo->findBy($criteria, $orderBy);
        return $articles;
    }

    //<editor-fold desc="Bibliographic Entries Handling">
    public function refreshSortNumbers($category) {
        $sortNumber = 1;

        $articles = $this->getByCategory($category);

        foreach ($articles as $article) {
            $article->setPosition($sortNumber);
            $sortNumber++;
            $this->em->persist($article);
        }
        $this->em->flush();
    }

    public function refreshBibliographicEntries($entity) {
        if (is_int($entity)) $article = $this->get($entity);
        elseif ($entity instanceof Article or $entity instanceof Draft) $article = $entity;
        else throw new NotFoundHttpException('Resource Not Found');
    
        $structure = $this->handler->makeContentStructure($article);
        $editors = $this->getEditors($structure);
        $content = $article->getContent();

        if ($content != null) {
            $modifiedContent = array();
            $this->position = 1;
            $this->bEntries = array();

            $introduction = $article->getIntroduction();
            $modifiedIntroduction = $this->searchAndReplace($introduction, $article);
    
            
            foreach($editors as $editor) {
                if (array_key_exists($editor, $content)) {
                    $modifiedContent[$editor] = $this->searchAndReplace($content[$editor], $article);
                }
            }

            $attached = $article->getAttached();
            $modifiedAttached = $this->searchAndReplace($attached, $article);

            $article->setIntroduction($modifiedIntroduction);
            $article->setContent($modifiedContent);
            $article->setAttached($modifiedAttached);

            $this->cleanBEntries($article);

            parent::save($article);
        }
    }

    public function swapPositions($article1, $article2) {
        $p1 = $article1->getPosition();
        $p2 = $article2->getPosition();

        $article1->setPosition($p2);
        $article2->setPosition($p1);

        $this->em->persist($article1);
        $this->em->persist($article2);
        $this->em->flush();
    }
    //</editor-fold>

    //<editor-fold desc="Permissions Handling">
    /**
     * @param Draft|Article $entity
     * @return null|void
     * @throws \Symfony\Component\Security\Core\Exception\InvalidArgumentException
     */
    public function setPermissions($entity)
    {
        $aclManager = $this->aclManager;

        if ($entity instanceof Draft) $article = $entity->getArticle();
        elseif ($entity instanceof Article) $article = $entity;
        else throw new InvalidArgumentException('The entity must be an Article or a Draft, given ' . get_class($entity));

        $owner = $article->getOwner();
        $supervisor = $article->getSupervisor();
        $editor = $article->getEditor();

        $users = array();
        $users[] = $owner;

        $aclManager->clean($entity, $users);

        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), User::ROLE_ADMIN);
        $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), User::ROLE_SUPER_ADMIN);
        if ($owner) $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), $owner);
        if ($supervisor && $supervisor != $owner) {
            $aclManager->grant($entity, array(PermissionMap::PERMISSION_EDIT), $supervisor);
            $users[] = $supervisor;
        }

        if ($editor && !$this->inArray($editor, $users)) {
            $aclManager->grant($entity, array(PermissionMap::PERMISSION_CONTENT), $editor);
        }
    }

    /**
     * @param Draft|Article $entity
     * @param  $users
     * @throws \Symfony\Component\Security\Core\Exception\InvalidArgumentException
     */
    public function updatePermissions($entity, $users)
    {
        if ($entity instanceof Draft) $article = $entity->getArticle();
        elseif ($entity instanceof Article) $article = $entity;
        else throw new InvalidArgumentException('The entity must be an Article or a Draft, given ' . get_class($entity));

        $aclManager = $this->aclManager;

        $owner = null;
        $supervisor = null;
        $editor = null;

        foreach ($users as $key => $user) {
            switch ($key) {
                case 'owner':
                    $owner = $user;
                    break;
                case 'supervisor':
                    $supervisor = $user;
                    break;
                case 'editor':
                    $editor = $user;
                    break;
            }
        }

        $oldUsers = array();

        if ($owner) {
            $aclManager->revoke($article, array('owner'), $owner['old']);
            $oldUsers[] = $owner['old'];
        }

        if ($supervisor) {
            if ($supervisor['old'] && !$this->inArray($supervisor['old'], $oldUsers)) {
                $aclManager->revoke($article, array(PermissionMap::PERMISSION_EDIT), $supervisor['old']);
                $oldUsers[] = $supervisor['old'];
            }
        }

        if ($editor && $editor['new']) {
            if ($editor['old'] && !$this->inArray($editor['old'], $oldUsers)) {
                $aclManager->revoke($article, array(PermissionMap::PERMISSION_EDIT), $editor['old']);
                $oldUsers[] = $editor['old'];
            }
        }

        $this->setPermissions($article);
    }

    private function inArray(User $user, $array) {
        foreach ($array as $currentUser) {
            if ($currentUser == $user) return true;
        }

        return false;
    }
    //</editor-fold>

    //<editor-fold desc="Editors Handling">
    /**
     * Search for bibliographic entries and update his position
     *
     * @param string  $content
     * @param Article $article
     *
     * @return string
     */
    private function searchAndReplace($content, $article) {
        $document = new \DOMDocument();
        $document->encoding = 'utf-8';
        try {
            $document->loadHTML(utf8_decode($content));

            $elements = $document->getElementsByTagName('a');
    
            /** @var \DOMElement $element */
            foreach($elements as $element) {
                $uid = $element->attributes->getNamedItem('data-bibliography-uid');
    
                if ($uid) {
                    $uid = $uid->nodeValue;
                    $repo = $this->em->getRepository('BackendBundle:BibliographicEntry');
                    $bEntry = $repo->findOneBy(array('uid' => $uid));

                    if ($bEntry) {
                        if (array_key_exists($uid, $this->bEntries)) {
                            $position = $this->bEntries[$uid];
                        } else {
                            $position = $this->position;
                            $this->bEntries[$uid] = $position;
                            $this->position++;
                        }
                        
                        if ($bEntry->getPosition() != $position) {
                            $bEntry->setPosition($position);
                            $this->em->persist($bEntry);
                            $this->em->flush();
                        }
                        
                        if (!$article->hasBibliographicEntry($bEntry)) {
                            $article->addBibliographicEntry($bEntry);
                            $this->em->persist($article);
                            $this->em->flush();
                        }
    
                        $element->setAttribute('name', 'BAKentry-' . $uid);

                        $link = $document->createElement('a', $position);
                        $link->setAttribute('data-bibliography-uid', $uid);
                        $link->setAttribute('name', 'entry-' . $uid);
                        $link->setAttribute('href', '#'.$uid);
                        $link->setAttribute('class', 'reference-link');

                        $element->parentNode->replaceChild($link, $element);
                    } else {
                        if ( $this->container->get('kernel')->getEnvironment() == "dev" ) dump($uid);
                        $element->parentNode->removeChild($element);
                    }
                }
            }

            $html = $document->saveHTML($document);
            $layout = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);

            return $layout;

        } catch (\Exception $e) {
            if ( $this->container->get('kernel')->getEnvironment() == "dev" ) dump($e->getMessage());
            return $content;
        }
    }

    private function cleanBEntries(&$article) {
        $allBEntries = $article->getBibliographicEntries();

        foreach($allBEntries as $entry) {
            $uid = $entry->getUid();
            if (!array_key_exists($uid, $this->bEntries)) {
                $article->removeBibliographicEntry($entry);
                //$this->em->remove($entry);
            }
        }

        $this->em->flush();
    }

    private function getEditors($structure) {
        $editors = array();

        foreach($structure as $key => $item ) {
            if (array_key_exists('fields', $item)) {
                $editors = array_merge($editors, $this->getEditors($item['fields']));
            } else {
                if ($item['type'] == 'ckeditor') $editors[] = $key;
            }
        }

        return $editors;
    }
    //</editor-fold>
}