<?php

namespace Uzink\UtilsBundle\EventListener;

use Uzink\UtilsBundle\Event\MailingEvent;

class MailingEventListener
{
    protected $mailer;
    protected $twig;
    
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }
    
    public function onNewInfoRequest(MailingEvent $event) {
        $body = "Ha habido un nuevo contacto, por favor visite la web de administración para comprobarlo";
        
        $message = \Swift_Message::newInstance()
            ->setSubject('Creada nueva sesión')
            ->setFrom('info@empark.com')
            ->setTo($event->getReceptor())
            ->setBody($body);
        $this->mailer->send($message);          
    }
    
    public function onNewSession(MailingEvent $event)
    {
        $sesion = $event->getEntity();
        $terapeuta = $sesion->getTerapeuta();
        $paciente = $sesion->getPaciente();
        $fechaHora =  $sesion->getFechaHora();
        $fecha = $fechaHora->format('d/m/Y');
        $hora = $fechaHora->format('H:i');
        
        $templateFile = 'ProunUsuarioBundle:Default:email.html.twig';
        $templateContent = $this->twig->loadTemplate($templateFile);
        
        $body = $templateContent->render(
            array(
                'title' => 'Nueva sesión',
                'paragraphs' => array(
                    'Se ha creado una nueva sesión, con los siguientes datos'
                ),
                'featured_paragraphs' => array(
                    array('text' => 'Terapeuta', 'featured' => $terapeuta->getNombre()),
                    array('text' => 'Paciente', 'featured' => $paciente->getNombre()),
                    array('text' => 'Fecha', 'featured' => $fecha),
                    array('text' => 'Hora', 'featured' => $hora)
                ),
                'links' => array(
                    array('href' => 'http://www.persumonline.net', 'text' => 'Accede a la plataforma')
                ),
                'footer' => 'AVISO LEGAL: El contenido de este mensaje de correo electrónico, incluidos los ficheros adjuntos, es confidencial y está protegido por el artículo 18.3 de la Constitución Española, que garantiza el secreto de las comunicaciones. Si usted recibe este mensaje por error, por favor póngase en contacto con el remitente para informarle de este hecho, y no difunda su contenido ni haga copias. Este aviso legal ha sido incorporado automáticamente al mensaje.'
            )
        );
        
        $message = \Swift_Message::newInstance()
            ->setContentType("text/html")
            ->setSubject('Creada nueva sesión')
            ->setFrom('info@persumonline.net')
            ->setTo(array($paciente->getEmail(), $terapeuta->getEmail()))
            ->setBody($body);
        $this->mailer->send($message);        
    }
}