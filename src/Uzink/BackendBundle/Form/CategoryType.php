<?php

namespace Uzink\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
    private $id;
    private $user;
    
    public function __construct($id = null, $user = null) {
        $this->id = $id;
        $this->user = $user;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Bloque Artículo
            ->add(
                $builder
                    ->create('block_category', 'block', 
                        array(
                            'icon' => 'icon-article-category',
                            'label' => 'category.blocks.category',
                            'virtual' => true
                        ))
                    ->add('title',
                          'text',
                          array(
                              'label' => 'category.title'
                          ))
                    ->add('owner',
                          'entity',
                          array(
                              'label' => 'category.leader',
                              'class' => 'BackendBundle:User',
                              'empty_value' => 'Selecciona un usuario',
                              'query_builder' => function(EntityRepository $er) {
                                    return $er->findUsersByRoleQB('ROLE_LEADER');
                              }
                          ))                    
                    ->add('parent',
                          'parentCategory',
                          array(
                              'label' => 'category.parent',
                              'class' => 'BackendBundle:Category',
                              'id' => $this->id
                          ))
                    ->add('imageResource',
                          'imagepicker',
                          array(
                              'label' => 'category.image',
                              'required' => false,
                              'empty_data' => null,
                              'size' => array(
                                  'height' => 140,
                                  'width' => 140
                              )
                          ))
                    ->add('introduction',
                        'ckeditor',
                        array(
                            'label' => 'category.introduction'
                        ))
                    ->add('description',
                        'ckeditor',
                        array(
                            'label' => 'category.description'
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
            )
            // Bloque SEO
            ->add(
                $builder
                    ->create('block_seo', 'seo', 
                                array(
                                    'icon' => 'icon-search',
                                    'label' => 'category.blocks.seo',
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
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Category',
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'category';
    }
}