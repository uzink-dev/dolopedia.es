<?php

namespace Uzink\BackendBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Form\ChoiceList\CategoryChoiceList;
use Uzink\BackendBundle\Form\DataTransformer\CategoryToNumberTransformer;

class CategoryType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $transformer = new CategoryToNumberTransformer($this->entityManager);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'class'         => 'BackendBundle:Category',
            'choice_list'   => new CategoryChoiceList($this->entityManager)
        ));
    }


    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'category';
    }
}
