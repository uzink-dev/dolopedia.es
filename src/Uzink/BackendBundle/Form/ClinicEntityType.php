<?php
namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClinicEntityType extends AbstractType
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
                    // Acordeón Definicion
                    ->add(
                        $builder
                            ->create('acItem_definition', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.definition',
                                            'virtual' => true
                                        ))
                            ->add('definition',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.clinicEntity.definition',
                                ))
                            ->add('painLocation',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.painLocation',
                                  ))         
                            ->add('affectedSystem',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.affectedSystem',
                                  ))                             
                    )   
                    // Acordeón Epidemiología
                    ->add(
                        $builder
                            ->create('acItem_epidemiology', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.epidemiology',
                                            'virtual' => true
                                        ))        
                            ->add('epidemiology',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.blocks.epidemiology',
                                  ))         
                    )   
                    // Acordeón Etiopatogenia
                    ->add(
                        $builder
                            ->create('acItem_aetiopathogenesis', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.aetiopathogenesis',
                                            'virtual' => true
                                        ))        
                            ->add('aetiopathogenesis',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.blocks.aetiopathogenesis',
                                  ))         
                    ) 
                    // Acordeón Factores Precipitantes
                    ->add(
                        $builder
                            ->create('acItem_precipitatingFactors', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.precipitatingFactors',
                                            'virtual' => true
                                        ))        
                            ->add('precipitatingFactors',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.blocks.precipitatingFactors',
                                  ))         
                    )                     
                    // Acordeón Clínica
                    ->add(
                        $builder
                            ->create('acItem_clinic', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.clinic',
                                            'virtual' => true
                                        ))        
                            ->add('clinicPearls',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.clinicPearls',
                                  ))         
                    )                     
                    // Acordeón Complicaciones
                    ->add(
                        $builder
                            ->create('acItem_complications', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.complications',
                                            'virtual' => true
                                        ))        
                            ->add('complications',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.blocks.complications',
                                  ))         
                    ) 
                    // Acordeón Diagnóstico
                    ->add(
                        $builder
                            ->create('acItem_diagnosis', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.diagnosis',
                                            'virtual' => true
                                        ))        
                            ->add('anamnesis',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.anamnesis',
                                  ))     
                            ->add('physicalExamination',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.physicalExamination',
                                  ))  
                            ->add('complementaryExploration',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.complementaryExploration',
                                  ))                              
                    )  
                    // Acordeón Diagnóstico Diferencial
                    ->add(
                        $builder
                            ->create('acItem_diferentialDiagnosis', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.diferentialDiagnosis',
                                            'virtual' => true
                                        ))        
                            ->add('diferentialDiagnosis',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.blocks.diferentialDiagnosis',
                                  ))         
                    ) 
                    // Acordeón Pronóstico
                    ->add(
                        $builder
                            ->create('acItem_forecast', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.forecast',
                                            'virtual' => true
                                        ))        
                            ->add('forecast',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.blocks.forecast',
                                  ))         
                    ) 
                    // Acordeón Prevención
                    ->add(
                        $builder
                            ->create('acItem_prevention', 'accordion', 
                                        array(
                                            'label' => 'article.types.clinicEntity.blocks.prevention',
                                            'virtual' => true
                                        ))        
                            ->add('prevention',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.clinicEntity.blocks.prevention',
                                  ))         
                    ) 
                    // Acordeón Tratamienton
                    ->add(
                        $builder
                            ->create(
                                'acItem_treatment',
                                'accordion',
                                array(
                                    'label' => 'article.types.clinicEntity.blocks.treatment',
                                    'virtual' => true
                                )
                            )
                            ->add(
                                'generalities',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.clinicEntity.generalities',
                                )
                            )
                            ->add(
                                $builder->create(
                                    'therapeuticStrategies',
                                    'separator',
                                    array(
                                        'label' => 'article.types.clinicEntity.therapeuticStrategies',
                                        'virtual' => true,
                                    )
                                )
                                ->add(
                                    $builder
                                        ->create(
                                            'conservativeTreatment',
                                            'separator',
                                            array(
                                                'label' => 'article.types.clinicEntity.conservativeTreatment',
                                                'virtual' => true,
                                            )
                                        )
                                        ->add('pharmacological',
                                            'ckeditor',
                                            array(
                                                'label' => 'article.types.clinicEntity.pharmacological',
                                            )
                                        )
                                        ->add('notPharmacological',
                                            'ckeditor',
                                            array(
                                                'label' => 'article.types.clinicEntity.notPharmacological',
                                            )
                                        )
                                )
                                ->add('interventionalTreatment',
                                    'ckeditor',
                                    array(
                                        'label' => 'article.types.clinicEntity.interventionalTreatment',
                                    )
                                )
                                ->add('surgery',
                                    'ckeditor',
                                    array(
                                        'label' => 'article.types.clinicEntity.surgery',
                                    )
                                )
                            )
                            ->add('interventionAlgorithm',
                                'ckeditor',
                                array(
                                    'label' => 'article.types.clinicEntity.interventionAlgorithm',
                                )
                            )
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
        return 'uzink_backendbundle_techniquestype';
    }
}