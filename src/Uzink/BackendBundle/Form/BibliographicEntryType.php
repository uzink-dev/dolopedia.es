<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BibliographicEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('uid')
            ->add('title')
            ->add('author')
            ->add('publication')
            ->add('volume')
            ->add('pages')
            ->add('position')
            ->add('year')
            ->add('link', 'url');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\BibliographicEntry'
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_bibliographicentrytype';
    }
}
