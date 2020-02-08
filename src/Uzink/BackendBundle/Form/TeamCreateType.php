<?php

namespace Uzink\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Uzink\BackendBundle\Entity\User;

class TeamCreateType extends UserAccountType
{
    private $user;
    private $translator;

    public function __construct(User $user, TranslatorInterface $translator) {
        $this->user = $user;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

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
            ->add($roleSelect)
            ->remove('type')
            ->remove('role')
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
                    'required' => true
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\User',
            'translation_domain' => 'dolopedia'
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_teamcreationtype';
    }
}
