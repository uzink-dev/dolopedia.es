<?php
namespace Uzink\UtilsBundle\Service;

class MailerService {
    private $mailer;
    private $twig;

    private $title;
    private $paragraphs;
    private $fParagraphs;
    private $links;
    private $footer;
    private $template;
    private $message;
    
    private $to;
    
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $templateFile = 'UtilsBundle:Email:email.html.twig';
        $this->template = $this->twig->loadTemplate($templateFile);        

    }
    
    public function reset() {
        $this->title = '';
        $this->paragraphs = array();
        $this->fParagraphs = array();
        $this->links = array();
        $this->to = array();
        $this->footer = 'AVISO LEGAL: El contenido de este mensaje de correo electrónico, incluidos los ficheros adjuntos, es confidencial y está protegido por el artículo 18.3 de la Constitución Española, que garantiza el secreto de las comunicaciones. Si usted recibe este mensaje por error, por favor póngase en contacto con el remitente para informarle de este hecho, y no difunda su contenido ni haga copias. Este aviso legal ha sido incorporado automáticamente al mensaje.';        
    }
    
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function addParagraph($paragraph) {
        $this->paragraphs[] = $paragraph;
    }
    
    public function addFParagraph($feature, $text) {
        $this->fParagraphs[] = array('text' => $feature, 'featured' => $text);
    }    
    
    public function addLink($url, $text) {
        $this->links[] = array('href' => $url, 'text' => $text);
    }
    
    public function addTo($email) {
        $this->to[] = $email;
    }
    
    public function send() {
        $content = array(
             'title' => $this->title,
             'paragraphs' => $this->paragraphs,
             'featured_paragraphs' => $this->fParagraphs,
             'links' => $this->links,
             'footer' => $this->footer
         );

         $body = $this->template->render($content);

        
        $this->message = \Swift_Message::newInstance()
            ->setContentType("text/html")
            ->setSubject($this->title)
            ->setFrom('d.fernandez@proun.es')
            ->setTo($this->to)
            ->setBody($body);                
        
        $response = $this->mailer->send($this->message);          
    }
}
