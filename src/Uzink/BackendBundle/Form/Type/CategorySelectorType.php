<?php

namespace Uzink\BackendBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Form\ChoiceList\CategoryChoiceList;
use Uzink\BackendBundle\Form\DataTransformer\CategoryToNumberTransformer;

class CategorySelectorType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'class'         => 'BackendBundle:Category'
        ));
    }


    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'category_selector';
    }
}
