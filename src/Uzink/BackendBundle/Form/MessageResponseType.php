<?php

namespace Uzink\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Entity\User;

class MessageResponseType extends AbstractType
{
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder
                    ->create('block_message', 'block',
                        array(
                            'icon' => 'icon-message-reply',
                            'label' => 'message.response',
                            'virtual' => true
                        ))
                    ->add('receiver',
                        'entity',
                        array(
                            'class' => 'BackendBundle:User',
                            'label' => 'message.receiver',
                            'disabled' => true
                        ))
                    ->add('subject',
                        null,
                        array(
                            'label' => 'message.subject',
                            'disabled' => true
                        ))
                    ->add('body',
                        'textarea',
                        array(
                            'label' => 'message.message'
                        ))
                    ->add('attachmentFile',
                        'file',
                        array(
                            'label' => 'message.attachment',
                            'required' => false
                        ))
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Uzink\BackendBundle\Entity\Message',
            'translation_domain' => 'dolopedia',
        ));
    }

    public function getName()
    {
        return 'uzink_backendbundle_messagetype';
    }
}
