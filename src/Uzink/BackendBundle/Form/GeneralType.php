<?php
namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GeneralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder
                    // Bloque Contenido
                    ->create('block_content', 'accordionBlock', 
                                array(
                                    'icon' => 'icon-article-content',
                                    'label' => 'article.blocks.content',
                                    'virtual' => true
                                ))
                    // Acordeón Contenido
                    ->add(
                        $builder
                            ->create('acItem_content', 'accordion',
                                        array(
                                            'label' => 'article.types.general.blocks.content',
                                            'virtual' => true
                                        ))
                            ->add('content',
                                  'ckeditor',
                                  array(
                                    'label' => 'article.types.general.content',
                                ))
                    )
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'dolopedia',
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_general';
    }
}