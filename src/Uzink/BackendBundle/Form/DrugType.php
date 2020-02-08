<?php
namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DrugType extends AbstractType
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
                    // Acordeón Introducción
                    ->add(
                        $builder
                            ->create('acItem_introduction', 'accordion',
                                array(
                                    'label' => 'article.types.drug.general',
                                    'virtual' => true
                                ))
                            ->add('general',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.general',
                                ))
                    )
                    // Acordeón Nombre comercial y forma farmacéutica
                    ->add(
                        $builder
                            ->create('acItem_nameAndDosage', 'accordion',
                                array(
                                    'label' => 'article.types.drug.nameAndDosage',
                                    'virtual' => true
                                ))
                            ->add('nameAndDosage',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.nameAndDosage',
                                ))
                    )
                    // Acordeón Características químicas
                    ->add(
                        $builder
                            ->create('acItem_chemicalCharacteristics', 'accordion',
                                array(
                                    'label' => 'article.types.drug.chemicalCharacteristics',
                                    'virtual' => true
                                ))
                            ->add('chemicalCharacteristics',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.chemicalCharacteristics',
                                ))
                    )
                    // Acordeón Farmacocinética
                    ->add(
                        $builder
                            ->create('acItem_pharmacokinetics', 'accordion',
                                array(
                                    'label' => 'article.types.drug.pharmacokinetics',
                                    'virtual' => true
                                ))
                            ->add('pharmacokinetics',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.pharmacokinetics',
                                ))
                    )
                    // Acordeón Mecanismo de acción
                    ->add(
                        $builder
                            ->create('acItem_mechanismOfAction', 'accordion',
                                array(
                                    'label' => 'article.types.drug.mechanismOfAction',
                                    'virtual' => true
                                ))
                            ->add('mechanismOfAction',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.mechanismOfAction',
                                ))
                    )
                    // Acordeón Lugar de acción
                    ->add(
                        $builder
                            ->create('acItem_placeOfAction', 'accordion',
                                array(
                                    'label' => 'article.types.drug.placeOfAction',
                                    'virtual' => true
                                ))
                            ->add('placeOfAction',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.placeOfAction',
                                ))
                    )
                    // Acordeon de Acciones farmacológicas
                    ->add(
                        $builder
                            ->create('acItem_pharmacologicalActions', 'accordion',
                                array(
                                    'label' => 'article.types.drug.blocks.pharmacologicalActions',
                                    'virtual' => true
                                ))
                            ->add('analgesicEffect',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.analgesicEffect',
                                ))
                            ->add('otherClinicalEffects',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.otherClinicalEffects',
                                ))
                    )
                    // Acordeón Posología y administración
                    ->add(
                        $builder
                            ->create('acItem_dosageAndAdministration', 'accordion',
                                array(
                                    'label' => 'article.types.drug.dosageAndAdministration',
                                    'virtual' => true
                                ))
                            ->add('dosageAndAdministration',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.dosageAndAdministration',
                                ))
                    )
                    // Acordeón Efectos adversos
                    ->add(
                        $builder
                            ->create('acItem_adverseEffects', 'accordion',
                                array(
                                    'label' => 'article.types.drug.adverseEffects',
                                    'virtual' => true
                                ))
                            ->add('adverseEffects',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.adverseEffects',
                                ))
                    )
                    // Acordeón Interacciones
                    ->add(
                        $builder
                            ->create('acItem_interactions', 'accordion',
                                array(
                                    'label' => 'article.types.drug.interactions',
                                    'virtual' => true
                                ))
                            ->add('interactions',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.interactions',
                                ))
                    )
                    // Acordeón Indicaciones
                    ->add(
                        $builder
                            ->create('acItem_indications', 'accordion',
                                array(
                                    'label' => 'article.types.drug.indications',
                                    'virtual' => true
                                ))
                            ->add('indications',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.indications',
                                ))
                    )
                    // Acordeón Contraindicaciones
                    ->add(
                        $builder
                            ->create('acItem_contraindications', 'accordion',
                                array(
                                    'label' => 'article.types.drug.contraindications',
                                    'virtual' => true
                                ))
                            ->add('contraindications',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.drug.contraindications',
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