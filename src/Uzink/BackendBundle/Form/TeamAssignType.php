<?php

namespace Uzink\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Entity\UserRepository;

class TeamAssignType extends AbstractType
{
    private $user;
    private $translator;
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(User $user, TranslatorInterface $translator, UserRepository $repository) {
        $this->user = $user;
        $this->translator = $translator;
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $role = $this->user->getRole();
        $roles = $this->user->getHierarchyRoles($role);

        $choices = array();
        $roleSelect = $builder->create('roleSelect', 'form', array('mapped' => false));
        foreach($roles as $role) {
            if ($role != 'ROLE_USER' && $role != $this->user->getRole()) {
                $choices[$role] = 'team.roles.'.$role;
                $nextRole = User::getNextRole($role);
                $roleTrans = $this->translator->trans('team.roles.' . $nextRole, array(), 'dolopedia');
                $roleSelect->add(
                     $role,
                     'entity',
                     array(
                          'label' => $this->translator->trans('team.parentUser', array('%role%' => $roleTrans), 'dolopedia'),
                          'class' => 'BackendBundle:User',
                          'mapped' => false,
                          'query_builder' => function(EntityRepository $er ) use ($nextRole) {
                              return $er->findChildrenByRoleQB($nextRole, $this->user);
                          },
                          'attr' => array(
                              'class' => 'form-control',
                          ),
                          'empty_value' => 'team.selectParentUser',
                          'required' => false
                      )
                );
            }
        }

        $builder
            ->add(
                'user',
                'extended_entity',
                array(
                    'label' => 'team.selectUser',
                    'class' => 'BackendBundle:User',
                    'mapped' => false,
/*                    'query_builder' => function(EntityRepository $er ) {
                            return $er->findUsersByRoleQB(User::ROLE_USER, null, true);
                        },*/
                    'attr' => array(
                        'class' => 'select2-control',
                    ),
                    'empty_value' => 'team.selectUser',
                    'required' => true
                )
            )
            ->add($roleSelect)
            ->remove('type')
            ->add('type',
                'choice',
                array(
                    'label' => 'user.type',
                    'choices' => $choices,
                    'attr' => array(
                        'class' => 'form-control',
                        'data-extended-value' => 'extendedTipoUsuario'
                    ),
                    'empty_value' => 'team.selectType',
                    'required' => true,
                    'mapped' => false,

            ))
            ->add('articles', 'entity', array(
                'mapped' => false,
                'label' => 'article.unassigned',
                'class' => 'BackendBundle:Article',
                'multiple' => true,
                'query_builder' => function(EntityRepository $er) {
                                    return $er->queryUnassignedArticles($this->user);
                                   },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        if (!$ids = $this->repository->findInTeamUsers()) {
            return;
        }

        foreach ($view->children['user']->vars['choices'] as $key => $user) {
            if (in_array($user->value, $ids)) {
                $user->attr['disabled'] = 'disabled';
            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'mapped' => false,
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_teamassigntype';
    }
}
