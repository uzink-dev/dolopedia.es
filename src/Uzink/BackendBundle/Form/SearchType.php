<?php

namespace Uzink\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'keyword',
                TextType::class,
                array(
                    'label' => 'article.search.form.keyword',
                    'required' => false
                ))
            ->add(
                'engine',
                EntityType::class,
                array(
                    'label' => 'article.search.form.engine',
                    'class' => 'Uzink\BackendBundle\Entity\Engine',
                    'empty_value' => 'Dolopedia',
                    'required' => false
                ))
            ->add(
                'category',
                'category_selector',
                array(
                    'label' => 'article.search.form.category',
                    'class' => 'Uzink\BackendBundle\Entity\Category',
                    'empty_value' => 'article.search.form.category.placeholder',
                    'required' => false
                ))
            ->add(
                'author',
                TextType::class,
                array(
                    'label' => 'article.search.form.author',
                    'required' => false
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'mapped' => false,
            'csrf_protection' => false,
            'translation_domain' => 'dolopedia',
        ));
    }

    public function getName()
    {
        return '';
    }
}
