<?php

namespace Uzink\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Uzink\BackendBundle\Entity\Request;

class ArticleRequestAcceptType extends AbstractType
{
    private $label;
    private $role;
    private $assignedUser;

    public function __construct(Request $request, TranslatorInterface $translator) {
        switch ($request->getType()) {
            case Request::TYPE_REQUEST_NEW:
                $this->label = $translator->trans('request.supervisorAssign', array(), 'dolopedia');
                $this->role = 'ROLE_SUPERVISOR';
                $this->assignedUser = true;
                break;

            case Request::TYPE_REQUEST_MODIFY:
                $this->label = $translator->trans('request.editorAssign', array(), 'dolopedia');
                $this->role = 'ROLE_EDITOR';

                if ($request->getArticle()->getEditor()) $this->assignedUser = false;
                else $this->assignedUser = true;

                break;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->assignedUser) {
            $builder
                ->add('assignedUser',
                      'entity',
                      array(
                          'label' => $this->label,
                          'class' => 'BackendBundle:User',
                          'query_builder' => function(EntityRepository $er ) {
                              return $er->findUsersByRoleQB($this->role);
                          }
                    ));
        }
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
        return 'article_request_accept';
    }
}