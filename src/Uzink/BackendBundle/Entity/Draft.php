<?php

namespace Uzink\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Draft
 *
 * @ORM\Entity
 * @ORM\Table(name="draft")
 */
class Draft extends BaseArticle {
    const STATUS_DRAFTED = 'drafted';
    const STATUS_REVISION = 'revision';
    const STATUS_VALIDATED = 'validated';
    const STATUS_PUBLISHED = 'published';

    const STEP_DRAFT = 'draft';
    const STEP_REVISE = 'revise';
    const STEP_VALIDATE = 'validate';
    const STEP_PUBLISH = 'publish';

    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="\Uzink\BackendBundle\Entity\Article", inversedBy="drafts", cascade={"persist"})
     * @Assert\Valid
     */
    private $article;

    public function __construct($entity = null) {
        parent::__construct();

        if ($entity instanceof Article) {
            $this->article = $entity;
        }

        if ($entity instanceof Draft) {
            $this->article = $entity->getArticle();
        }

        if ($entity instanceof Article || $entity instanceof Draft) {
            $this->introduction = $entity->getIntroduction();
            $this->content      = $entity->getContent();
            $this->bibliographicEntries = $entity->getBibliographicEntries();
            $this->attached     = $entity->getAttached();
        }
    }

    public function __toString() {
        return $this->article->getTitle();
    }

    /**
     * Set article
     *
     * @param \Uzink\BackendBundle\Entity\Article $article
     *
     * @return Draft
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \Uzink\BackendBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    public function getTitle()
    {
        if ($this->article) return $this->article->getTitle();
        else return '';
    }

    public function getCategory()
    {
        return $this->article->getCategory();
    }

    public function getType()
    {
        return $this->article->getType();
    }

    public function getEditor()
    {
        return $this->article->getEditor();
    }
}
