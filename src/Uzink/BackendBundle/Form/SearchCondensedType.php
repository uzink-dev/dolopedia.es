<?php

namespace Uzink\BackendBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchCondensedType extends AbstractType
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
                ));
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
