<?php

namespace Uzink\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Translation\TranslatorInterface;
use Uzink\BackendBundle\Entity\Request;

class ArticleRequestDeclineType extends AbstractType
{
    private $request;
    private $translator;

    public function __construct(Request $request, TranslatorInterface $translator) {
        $this->request = $request;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userFrom',
                  'entity',
                  array(
                      'label' => 'request.to',
                      'disabled' => true,
                      'class' => 'Uzink\BackendBundle\Entity\User'
                  ))
            ->add('subject',
                  'text',
                  array(
                      'label' => 'request.subject',
                      'disabled' => true,
                      'data' => $this->translator->trans('request.subjectContent', array('%request%' => $this->request->getTitle()), 'dolopedia'),
                      'mapped' => false
                ))
            ->add('reasonToDecline',
                'textarea',
                array(
                    'label' => 'request.content'
                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Uzink\BackendBundle\Entity\Request',
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'article_request_decline';
    }
}