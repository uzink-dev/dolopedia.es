<?php

namespace A2lix\Form\Type;

use Symfony\Component\Form\FormView,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    A2lix\Form\EventListener\DefaultTranslationsListener;

/**
 * Regroup by locales, all translations fields
 *
 * @author David ALLIX
 */
class TranslationsType extends AbstractType
{
    private $translationsListener;
    private $locales;
    private $defaultLocale;
    private $defaultRequired;

    /**
     *
     * @param \A2lix\Form\EventListener\DefaultTranslationsListener $translationsListener
     * @param array $locales
     * @param string $defaultLocale
     * @param boolean $defaultRequired
     */
    public function __construct(DefaultTranslationsListener $translationsListener, array $locales, $defaultLocale, $defaultRequired)
    {
        $this->translationsListener = $translationsListener;
        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
        $this->defaultRequired = $defaultRequired;
    }

    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->translationsListener);
    }

    /**
     * 
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_locale'] = $options['default_locale'];
    }    

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'by_reference' => false,
            'required' => $this->defaultRequired,
            'empty_data' => new \Doctrine\Common\Collections\ArrayCollection(),
            'locales' => $this->locales,
            'default_locale' => $this->defaultLocale,
            'fields' => array(),
            'is_seo' => false,
            'field_type' => '',
        ));
    }

    public function getName()
    {
        return 'a2lix_translations';
    }
}
