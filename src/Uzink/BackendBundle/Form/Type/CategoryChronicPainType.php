<?php
/**
 * Created by PhpStorm.
 * User: Jose
 * Date: 17/08/2015
 * Time: 9:12
 */

namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\LoggingTranslator;
use Uzink\BackendBundle\Form\ChoiceList\CategoryChronicPainChoicelist;

class CategoryChronicPainType extends AbstractType
{
    /**
     * @var LoggingTranslator
     */
    private $translator;

    /**
     * CategoryChronicPainType constructor.
     */
    public function __construct(LoggingTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'choice_list'   => new CategoryChronicPainChoicelist($this->translator)
        ));
    }


    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'categoryChronicPain';
    }
}