<?php

namespace Uzink\BackendBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Uzink\BackendBundle\Entity\User;

class ReceiversType extends AbstractType
{
    /** @var User */
    private $currentUser;

    private $availableReceivers;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();

        if ($this->currentUser->hasRole(User::ROLE_ADMIN) || $this->currentUser->hasRole(User::ROLE_SUPER_ADMIN)) {
            $this->availableReceivers = $entityManager->getRepository('BackendBundle:User')->findAll();
        } else {
            $this->availableReceivers = $this->currentUser->getWorkgroupUsers();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Uzink\BackendBundle\Entity\User',
            'multiple' => true,
            'choices' => $this->availableReceivers
        ));
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['availableReceivers'] = $this->availableReceivers;
    }      
    
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'receivers';
    }
}
