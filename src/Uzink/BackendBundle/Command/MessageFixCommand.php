<?php
namespace Uzink\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Category;
use Uzink\BackendBundle\Entity\Message;
use Uzink\BackendBundle\Entity\MessageRepository;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Event\ActivityEvent;

class MessageFixCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('message:fix:multiple')
            ->setDescription('Fix multiple emails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var MessageRepository $repo */
        $messageRepo = $em->getRepository('BackendBundle:Message');
        $multipleMessages = $messageRepo->findBy(array('multiple' => true));

        /** @var Message $message */
        foreach ($multipleMessages as $message) {
            $output->writeln('Setting Metadata for: ' . $message->getId());
            $multipleSendMetadata = $message->getMultipleSendMetadata();

            if (!is_array($multipleSendMetadata)) $multipleSendMetadata = array();
            if (!array_key_exists('sent', $multipleSendMetadata)) $multipleSendMetadata['sent'] = array();
            if (!array_key_exists('readed', $multipleSendMetadata)) $multipleSendMetadata['readed'] = array();

            $receivers = $message->getMultipleReceivers();
            /** @var User $user */
            foreach ($receivers as $user) {
                if (!array_key_exists($user->getId(), $multipleSendMetadata['sent']))
                    $multipleSendMetadata['sent'][$user->getId()] = false;

                if (!array_key_exists($user->getId(), $multipleSendMetadata['readed']))
                    $multipleSendMetadata['readed'][$user->getId()] = false;
            }

            $message->setMultipleSendMetadata($multipleSendMetadata);
            $em->persist($message);
            $em->flush();
        }
    }
}