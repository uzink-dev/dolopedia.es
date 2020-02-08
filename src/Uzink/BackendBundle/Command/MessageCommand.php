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

class MessageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('message:send:multiple')
            ->setDescription('Send enqueued emails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var MessageRepository $repo */
        $messageRepo = $em->getRepository('BackendBundle:Message');
        $userRepo = $em->getRepository('BackendBundle:User');
        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $mailer = $this->getContainer()->get('uzink.mailer');

        $messages = $messageRepo->findMultiplePending();
        /** @var Message $message */
        foreach ($messages as $message) {
            $output->writeln('Starting sending process for messsage ID: ' . $message->getId());
            $multipleSendMetadata = $message->getMultipleSendMetadata();

            if (!$multipleSendMetadata) {
                $multipleSendMetadata = array(
                    'sent' => array(),
                    'readed' => array()
                );
                /** @var User $receiver */
                foreach ($message->getMultipleReceivers() as $receiver) {
                    $multipleSendMetadata['sent'][$receiver->getId()] = false;
                    $multipleSendMetadata['readed'][$receiver->getId()] = false;
                }
                $message->setMultipleSendMetadata($multipleSendMetadata);
                $em->persist($message);
                $em->flush();
                $output->writeln('-- Initialized send metadata');
            }

            foreach ($multipleSendMetadata as $id => $sent) {
                if (!$sent) {
                    $receiver = $userRepo->find($id);
                    $receivers = [$receiver];

                    $event = new ActivityEvent($message, Activity::TYPE_USER_NEW_MULTIPLE_MESSAGE, $receivers, $message->getSender());
                    $dispatcher->dispatch(Activity::EVENT_CREATE_ACTIVITIES, $event);

                    $mailer->sendNewMessage($receiver, $message);

                    $multipleSendMetadata['sent'][$id] = true;

                    $output->writeln('-- Created email and notification for user ID: ' . $id);
                }
            }

            $message->setMultipleSendMetadata($multipleSendMetadata);
            $message->setMultipleComplete(true);
            $em->persist($message);
            $em->flush();
            $output->writeln('Sending process ended for messsage ID: ' . $message->getId());
        }
    }
}