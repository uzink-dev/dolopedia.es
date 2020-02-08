<?php
namespace Uzink\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ButtonTypeExtension extends AbstractTypeExtension {
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setOptional(
                array('icon', 'context')
            )
            ->addAllowedTypes(array(
                'icon' => 'string',
                'context' => 'string'
            ))
            ->setDefaults(array(
                'context' => 'btn-primary'
            ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['icon'] = $options['icon'];
        $view->vars['context'] = $options['context'];
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'button';
    }
}