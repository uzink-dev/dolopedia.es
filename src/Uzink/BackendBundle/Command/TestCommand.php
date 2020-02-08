<?php
namespace Uzink\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Uzink\BackendBundle\Entity\BibliographicEntry;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "app/console")
            ->setName('dolopedia:test')

            // the short description shown while running "php app/console list"
            ->setDescription('Test functionalities')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('BackendBundle:BibliographicEntry');
        $bEntry = $repo->findOneBy(array('uid' => 'f2697366-52a7-6954-c466-490f5c9e9c18'));
        
        die(var_dump($bEntry));
        
        // get all elements used for the notification email
        $title = "You have won the lottery!";
        $content = "Congratulation John! You have won 7'000'000$ in the lottery";
        $gotoUrl = "http://www.acmelottery.com/claim/you/money";
        $recipientId = 33; // user id

        // get your implementation of the AzineNotifierService
        $notifierService = $this->getContainer()->get('azine_email.example.notifier_service');
        $notifierService->addNotificationMessage($recipientId, $title, $content);

        $output->writeln('nada');
    }
}