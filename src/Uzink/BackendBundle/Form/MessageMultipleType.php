<?php

namespace Uzink\BackendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Uzink\BackendBundle\Entity\User;

class MessageMultipleType extends AbstractType
{
    private $user;

    private $availableReceivers;

    public function __construct(User $user) {
        $this->user = $user;
        $this->availableReceivers = $this->user->getWorkgroupUsers($this->user);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder
                    ->create('block_message', 'block',
                        array(
                            'icon' => 'icon-mail',
                            'label' => 'message.message',
                            'virtual' => true
                        ))
                    ->add('multipleReceivers', 'receivers',
                        array(
                            'label' => 'message.receiver',
                        ))
                    ->add('subject',
                        null,
                        array(
                            'label' => 'message.subject'
                        ))
                    ->add('body',
                        TextareaType::class,
                        array(
                            'label' => 'message.message'
                        ))
                    ->add('attachmentFile',
                        FileType::class,
                        array(
                            'label' => 'message.attachment',
                            'required' => false
                        ))
                    ->add('multiple', HiddenType::class, array('data' => true))
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
