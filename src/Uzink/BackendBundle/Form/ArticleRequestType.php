<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Form\ChoiceList\ArticleTypesChoiceList;
use Uzink\BackendBundle\Form\TechniquesType;
use Uzink\BackendBundle\Form\BibliographicEntryType;
use Uzink\BackendBundle\Entity\Article;

class ArticleRequestType extends AbstractType
{
    private $articleId = null;
    private $articleTitle = null;

    public function __construct(Article $article = null) {
        if ($article) {
            $this->articleId = $article->getId();
            $this->articleTitle = $article->getTitle();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('article',
                  'hidden_entity',
                  array(
                      'class' => 'Uzink\\BackendBundle\\Entity\\Article'
                  ))
            ->add('type',
                  'hidden',
                  array(
                      'data' => ($this->articleId)?Request::TYPE_REQUEST_MODIFY:Request::TYPE_REQUEST_NEW
                  ))
            ->add('title',
                'text',
                array(
                    'label' => 'article.title',
                    'data' => $this->articleTitle,
                    'read_only' => ($this->articleId)?true:false
                ))
            ->add('category',
                  'category',
                  array(
                      'label' => 'article.category2',
                      'class' => 'Uzink\BackendBundle\Entity\Category',
                      'read_only' => ($this->articleId)?true:false
                  ))
            ->add('content',
                  'textarea',
                  array(
                      'label' => 'request.comment',
                  ))
            ->add('attachmentFile',
                'file',
                array(
                    'label' => 'request.attachment',
                ))
            ->add(
                'userFrom',
                'hidden_entity',
                array(
                    'class' => 'Uzink\\BackendBundle\\Entity\\User'
                )
            )
            ->add(
                'userTo',
                'hidden_entity',
                array(
                    'class' => 'Uzink\\BackendBundle\\Entity\\User'
                )
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Request',
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'article_request';
    }
}