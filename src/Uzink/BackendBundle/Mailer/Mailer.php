<?php

namespace Uzink\BackendBundle\Mailer;


use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Message;
use Uzink\BackendBundle\Entity\Request;
use Uzink\BackendBundle\Entity\User;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Swift_Mailer
     */
    protected $spooledMailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Swift_Transport
     */
    protected $transport;

    /**
     * @var string
     */
    protected $fromEmail;

    public function __construct(\Swift_Mailer $mailer, \Swift_Mailer $spooledMailer, UrlGeneratorInterface $router, \Twig_Environment $twig, \Swift_Transport $transport, $fromEmail)
    {
        $this->mailer = $mailer;
        $this->spooledMailer = $spooledMailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->transport = $transport;
        $this->fromEmail = $fromEmail;
    }

    public function sendRequestEmail(UserInterface $user, Request $request)
    {
        $template = 'FrontBundle:Email:email.request.new.html.twig';
        $url = $this->router->generate('workflow.request.response', array('id' => $request->getId()), true);

        $context = array(
            'user' => $user,
            'request' => $request,
            'url' => $url
        );

        $this->sendMessage($template, $context,  $this->fromEmail, $user->getEmail());
    }

    public function sendRequestResponseEmail(UserInterface $user, Request $request)
    {
        $template = 'FrontBundle:Email:email.request.response.html.twig';
        $url = $this->router->generate('panel.collaboration.index', array(), true);

        $context = array(
            'user' => $user,
            'request' => $request,
            'url' => $url
        );

        $this->sendMessage($template, $context,  $this->fromEmail, $user->getEmail());
    }

    public function sendNewAssigmentEmail(UserInterface $user, Article $article)
    {
        $template = 'FrontBundle:Email:email.article.assigment.html.twig';
        $url = $this->router->generate('workflow.article.edit', array('id' => $article->getId()), true);

        $context = array(
            'user' => $user,
            'article' => $article,
            'url' => $url
        );

        $this->sendMessage($template, $context, $this->fromEmail, $user->getEmail());
    }

    public function sendValidationEmail(UserInterface $user, Article $article)
    {
        $template = 'FrontBundle:Email:email.article.validation.html.twig';
        $url = $this->router->generate('workflow.article.edit', array('id' => $article->getId()), true);

        $context = array(
            'user' => $user,
            'article' => $article,
            'url' => $url
        );

        $this->sendMessage($template, $context, $this->fromEmail, $user->getEmail());
    }

    public function sendPublicationEmail(UserInterface $user, Article $article)
    {
        $template = 'FrontBundle:Email:email.article.validation.html.twig';
        $url = $this->router->generate('public.article.show', array('slug' => $article->getSeoSlug()), true);

        $context = array(
            'user' => $user,
            'article' => $article,
            'url' => $url
        );

        $this->sendMessage($template, $context, $this->fromEmail, $user->getEmail());
    }

    public function sendNewMessage(UserInterface $user, Message $message)
    {
        $template = 'FrontBundle:Email:email.message.new.html.twig';
        $url = $this->router->generate('panel.message.show', array('id' => $message->getId()), true);

        $context = array(
            'user' => $user,
            'message' => $message,
            'url' => $url
        );

        $this->sendMessage($template, $context, $this->fromEmail, $user->getEmail(), $message->getMultiple());
    }

    public function sendNewUser(UserInterface $user, $administrators)
    {
        $template = 'FrontBundle:Email:email.user.new.html.twig';

        $context = array(
            'user' => $user
        );

        $adminEmails = array();
        foreach($administrators as $admin) {
            if ($admin instanceof User) $adminEmails[] = $admin->getEmail();
        }

        $this->sendMessage($template, $context, $this->fromEmail, $adminEmails);
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail, $spooled = false)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        if ($spooled) {
            $this->spooledMailer->send($message);
            $this->spooledMailer->getTransport()->stop();
        } else {
            $this->mailer->send($message);
            $this->mailer->getTransport()->stop();
        }
    }
}
