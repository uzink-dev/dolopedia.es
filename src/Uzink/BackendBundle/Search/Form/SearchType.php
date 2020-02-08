<?php
namespace Uzink\BackendBundle\Search\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Model\RequestSearch;

class SearchType extends AbstractType {
    private $searchChoices;

    public function __construct($searchChoices) {
        $this->searchChoices = $searchChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', 'hidden', array(
                'required' => false,
            ))
            ->add('direction', 'hidden', array(
                'required' => false,
            ))
            ->add('sortSelect','choice',array(
                'choices' => $this->searchChoices,
                'label' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'data-sort-select' => 'true'
                )
            ))
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $articleSearch = $event->getData();

                if(array_key_exists('sort',$articleSearch) && array_key_exists('direction',$articleSearch)){
                    $articleSearch['sortSelect'] = $articleSearch['sort'].' '.$articleSearch['direction'];
                }else{
                    $articleSearch['sortSelect'] = '';
                }

                $event->setData($articleSearch);
            });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName() {
        return '';
    }
} 