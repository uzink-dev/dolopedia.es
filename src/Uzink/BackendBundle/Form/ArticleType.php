<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Uzink\BackendBundle\Form\ChoiceList\ArticleTypesChoiceList;
use Uzink\BackendBundle\Entity\Article;

class ArticleType extends AbstractType
{
    private $router;

    private $tokenStorage;

    private $user;

    public function __construct(Router $router, TokenStorage $tokenStorage) {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->user = $tokenStorage->getToken()->getUser();
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Bloque Artículo
            ->add(
                $builder
                    ->create('block_article', 'block', 
                                array(
                                    'icon' => 'icon-article',
                                    'label' => 'article.blocks.article',
                                    'virtual' => true
                                ))
                    ->add('title',
                          null,
                          array(
                              'label' => 'article.title'
                          ))
            )
            // Bloque Categoría
            ->add(
                $builder
                    ->create('block_category', 'block', 
                                array(
                                    'icon' => 'icon-article-category',
                                    'label' => 'article.blocks.category',
                                    'virtual' => true
                                ))
                    ->add('category',
                          'category',
                          array(
                              'label' => 'article.category',
                              'class' => 'BackendBundle:Category',
                              'empty_value' => 'common.selectEmptyValue'
                          ))
                    ->add('type',
                          'choice',
                          array(
                              'label' => 'article.type',
                              'choice_list' => new ArticleTypesChoiceList(),
                              'empty_value' => 'common.selectEmptyValue',
                              'attr' => array(
                                  'data-ajax-target' => '.articleContent',
                                  'data-ajax-prevent' => '#contentWarning'
                              )
                          ))
            )   
            // Bloque Introducción
            ->add(
                $builder
                    ->create('block_introduction', 'block', 
                                array(
                                    'icon' => 'icon-article-intro',
                                    'label' => 'article.blocks.introduction',
                                    'virtual' => true
                                ))
                    ->add('introduction',
                          'ckeditor',
                          array(
                              'label' => 'article.introduction',
                          ))
                    ->add('seo_keywords',
                          'text',
                          array(
                              'label' => 'common.keywords'
                          )) 
            )
            // Bloque Introducción
            ->add(
                $builder
                    ->create('block_aditionalContent', 'block', 
                                array(
                                    'icon' => 'icon-article-additional',
                                    'label' => 'article.blocks.aditionalContent',
                                    'virtual' => true
                                ))
                    ->add('bibliographicEntries',
                          'bibliography',
                          array(
                              'label' => 'article.bibliography',
                              'type' => new BibliographicEntryType(),
                              'allow_add' => true,
                              'prototype' => true,
                              'by_reference' => false
                          ))                      
                    ->add('attached',
                          'ckeditor',
                          array(
                              'label' => 'article.attached'
                          )) 
            )
            // Bloque Asignación
            ->add(
                $builder
                    ->create('block_assigment', 'block', 
                                array(
                                    'icon' => 'icon-article-assign',
                                    'label' => 'article.blocks.assigment',
                                    'virtual' => true
                                ))
                    ->add('editor',
                        'entity',
                        array(
                            'label' => 'article.assignedUser',
                            'class' => 'Uzink\BackendBundle\Entity\User',
                            'empty_value' => 'article.selectAssignedUser',
                            'choices' => $this->user->getChildrenUsers(),
                            'attr' => array(
                                'data-field-type' => 'select2-control'
                            )
                        ))
            )  
            // Bloque SEO
            ->add(
                $builder
                    ->create('block_seo', 'seo', 
                                array(
                                    'icon' => 'icon-search',
                                    'label' => 'article.blocks.seo',
                                    'virtual' => true
                                ))
                    ->add('seoH1',
                          'text',
                          array(
                              'label' => 'article.seoTitle'
                          ))     
                    ->add('seoSlug',
                          'text',
                          array(
                              'label' => 'article.seoUrl'
                          )) 
                    ->add('seoKeywords',
                          'text',
                          array(
                              'label' => 'article.seoKeywords'
                          ))
                    ->add('seoDescription',
                          'textarea',
                          array(
                              'label' => 'article.seoDescription'
                          ))     
            )
            ->add(
                'save',
                'submit',
                array(
                    'label' => 'article.actions.saveDraft',
                    'icon'  => 'icon-save'
                ))
            ->add(
                'save_and_exit',
                'submit',
                array(
                    'label' => 'article.actions.saveAndExit',
                    'icon'  => 'icon-message-reply'
                ))
            ->add(
                'revise',
                'submit',
                array(
                    'label' => 'article.actions.revise',
                    'icon'  => 'icon-search',
                    'context' => 'btn-blue separate-right'
                ))
            ->add(
                'validate',
                'submit',
                array(
                    'label' => 'article.actions.validate',
                    'icon'  => 'icon-check',
                    'context' => 'btn-blue'
                ))
            ->add(
                'no_validate',
                'submit',
                array(
                    'label' => 'article.actions.noValidate',
                    'icon'  => 'icon-cancel',
                    'context' => 'btn-blue'
                ))
            ->add(
                'publish',
                'submit',
                array(
                    'label' => 'article.actions.publish',
                    'icon'  => 'icon-article',
                    'context' => 'btn-blue'
                ))
            ->add(
                'no_publish',
                'submit',
                array(
                    'label' => 'article.actions.noPublish',
                    'icon'  => 'icon-cancel',
                    'context' => 'btn-blue'
                ))
            ->add(
                'remove',
                'submit',
                array(
                    'label' => 'article.actions.remove',
                    'icon'  => 'icon-article-delete',
                    'context' => 'btn-dark-grey separate-top-md'
                ))
            ->add('_target',
                'hidden',
                array(
                    'mapped' => false,
                ));
        
        $formModifier = function (FormInterface $form, $type = null) use ($builder, $options) {
            $formType = null;
            switch ($type) {
                case Article::TYPE_TECHNIQUES:
                    $formType = new TechniquesType();
                    break;

                case Article::TYPE_CLINIC_ENTITY:
                    $formType = new ClinicEntityType();
                    break;

                case Article::TYPE_GENERAL:
                    $formType = new GeneralType();
                    break;

                case Article::TYPE_DRUG:
                    $formType = new DrugType();
                    break;
            }

            if ($formType) $form->add('content', $formType);
        };        
        
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $type = $event->getData()->getType();
                $formModifier($event->getForm(), $type);
            }
        );                
        
        $builder->get('block_category')->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $type = $event->getData();
                $formModifier($event->getForm()->getParent()->getParent(), $type);
            }
        );          
            
      
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Article',
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'article';
    }
}