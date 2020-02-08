<?php
namespace Uzink\BackendBundle\Search\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Entity\Message;

class MessageSearchType extends AbstractType {
    private $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->name == Message::RECEIVED) {
            $builder
                ->add(
                    $this->name . 'SortSelect','choice',array(
                    'choices' =>  array(
                        'all' => 'message.filter.all',
                        'readed' => 'message.filter.readed',
                        'notReaded' => 'message.filter.notReaded',
                    ),
                    'label' => 'message.filter.readed_unreaded',
                    'attr' => array(
                        'class' => 'form-control',
                    )
                ));
        }

        $builder
            ->add($this->name . 'DateFrom', 'datepicker', array('label' => 'message.filter.date'))
            ->add($this->name . 'DateTo', 'datepicker', array('label' => false));

        $builder->setMethod('GET');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'dolopedia',
            'csrf_protection' => false,
            'mapped' => false,
        ));
    }

    public function getName() {
        return '';
    }


} 