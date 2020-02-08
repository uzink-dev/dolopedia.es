<?php

namespace Uzink\BackendBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Form\ChoiceList\CategoryChoiceList;
use Uzink\BackendBundle\Form\DataTransformer\CategoryToNumberTransformer;

class ParentCategoryType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CategoryChoiceList
     */
    private $choiceList;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->choiceList->setInvisibleNode($options['id']);

        parent::buildForm($builder, $options);

        $transformer = new CategoryToNumberTransformer($this->entityManager);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array('id'));

        $this->choiceList = new CategoryChoiceList($this->entityManager, true);

        $resolver->setDefaults(array(
            'class'         => 'BackendBundle:Category',
            'choice_list'   => $this->choiceList,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['id'] = $options['id'];
    }


    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'parentCategory';
    }
}
