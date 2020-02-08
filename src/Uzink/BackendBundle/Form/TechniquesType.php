<?php
namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TechniquesType extends AbstractType
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
                    // Acordeón Anatomía
                    ->add(
                        $builder
                            ->create('acItem_anatomy', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.anatomy',
                                            'virtual' => true
                                        ))        
                            ->add('anatomy',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.anatomy',
                                  ))                
                    )
                    // Acordeón Posción del Paciente
                    ->add(
                        $builder
                            ->create('acItem_patientPosition', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.patientPosition',
                                            'virtual' => true
                                        ))        
                            ->add('patientPosition',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.patientPosition',
                                  ))                
                    )   
                    // Acordeon de Material
                    ->add(
                        $builder
                            ->create('acItem_material', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.material',
                                            'virtual' => true
                                        ))        
                            ->add('equipment',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.equipment',
                                  ))     
                            ->add('drugs',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.drugs',
                                  ))                              
                    )    
                    // Acordeón de Descripcion del Procedimiento
                    ->add(
                        $builder
                            ->create('acItem_procedureDescription', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.procedureDescription',
                                            'virtual' => true
                                        ))       
                            ->add('clinicPearls',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.clinicPearls',
                                  ))
                            ->add(
                                $builder->create(
                                    'pointLocatization',
                                    'separator',
                                      array(
                                          'label' => 'article.types.techniques.pointLocatization',
                                          'virtual' => true
                                      )
                                )
                                ->add(
                                    'radioscopy',
                                    'ckeditor',
                                    array(
                                        'label' => 'article.types.techniques.radioscopy',
                                    )
                                )
                                ->add(
                                    'echography',
                                    'ckeditor',
                                    array(
                                        'label' => 'article.types.techniques.echography',
                                    )
                                )
                            )
                            ->add(
                                $builder->create(
                                    'technicalRealization',
                                    'separator',
                                    array(
                                        'label' => 'article.types.techniques.technicalRealization',
                                        'virtual' => true
                                    )
                                )
                                ->add(
                                    'puncture',
                                    'ckeditor',
                                    array(
                                        'label' => 'article.types.techniques.puncture',
                                    )
                                )
                                ->add(
                                    'needleValidation',
                                    'ckeditor',
                                    array(
                                        'label' => 'article.types.techniques.needleValidation',
                                    )
                                )
                                ->add(
                                    'drugsOrProtocol',
                                    'ckeditor',
                                    array(
                                        'label' => 'article.types.techniques.drugsOrProtocol',
                                    )
                                )
                            )
                    )  
                    // Acordeon de Cuidados Posteriores
                    ->add(
                        $builder
                            ->create('acItem_aftercare', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.aftercare',
                                            'virtual' => true
                                        ))        
                            ->add('aftercare',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.aftercare',
                                  ))     
                    )                      
                    // Acordeon de Indicaciones
                    ->add(
                        $builder
                            ->create('acItem_indications', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.indications',
                                            'virtual' => true
                                        ))        
                            ->add('indications',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.indications',
                                  ))     
                    )  
                    // Acordeon de Contraindicaciones
                    ->add(
                        $builder
                            ->create('acItem_contraindications', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.contraindications',
                                            'virtual' => true
                                        ))        
                            ->add('contraindications',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.contraindications',
                                  ))     
                    )       
                    // Acordeon de Complicaciones
                    ->add(
                        $builder
                            ->create('acItem_complications', 'accordion', 
                                        array(
                                            'label' => 'article.types.techniques.complications',
                                            'virtual' => true
                                        ))        
                            ->add('complications',
                                  'ckeditor',
                                  array(
                                      'label' => 'article.types.techniques.complications',
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
        return 'uzink_backendbundle_techniquestype';
    }
}