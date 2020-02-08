<?php
// src/Acme/Sample/StoreBundle/Controller/SitemapsController.php
namespace Uzink\UtilsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Uzink\NoticiaBundle\Entity\Noticia;

class SitemapController extends Controller
{
    
    public function getOptions($route, $lang, $params = array())
    {
        
        if(!isset($params['translated_url']) || $params['translated_url'] === true)
        {
            $route = $route.'_'.$lang;
        }
        unset($params['translated_url']);
        return array(   'loc' => $this->get('router')->generate($route, array_merge(array('_locale' => $lang), $params)), 
                        'changefreq' => 'weekly', 
                        'priority' => '1');
    }

    public function sitemapAction($lang) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $idioma = $em->getRepository('IdiomaBundle:Idioma')->findByCodigo($lang);
        if(!$idioma)
        {
            return $this->redirect($this->generateUrl('error', array('error' => '404')), 404);
        }
        
        $urls = array();
        $hostname = $this->getRequest()->getHost();

        $options = array('_locale' => $lang, 
                              'changefreq' => 'weekly', 
                              'priority' => '0.7');
        

        $urls[] = $this->getOptions('homepage', $lang);
        $urls[] = $this->getOptions('noticias', $lang);
        $urls[] = $this->getOptions('productos', $lang);
//        $urls[] = $this->getOptions('contactar', $lang);
        $urls[] = $this->getOptions('faqs', $lang);
        $urls[] = $this->getOptions('aeropuertos', $lang);
        $urls[] = $this->getOptions('residentes', $lang);
        $urls[] = $this->getOptions('via_publica', $lang);
        $urls[] = $this->getOptions('aparcamiento_seccion', $lang);
        $urls[] = $this->getOptions('aparcamiento_buscar', $lang);
        $urls[] = $this->getOptions('promociones', $lang);

              
        foreach ($em->getRepository('CmsBundle:Cms')->findAll() as $cms)
        {
            $cms->setCurrentLocale($lang);
            if($cms->getSeoSlug())
            {
                $urls[] = $this->getOptions('cms_page', $lang, array('translated_url' => false, 'slug' => $cms->getSeoSlug()));
            }
        }
        
//        foreach ($em->getRepository('ParkingBundle:Parking')->findAllByTipo('rotacion') as $parking)
//        {
//            $parking->setCurrentLocale($lang);
//            if($parking->getSeoSlug())
//            {
//                $urls[] = $this->getOptions('residentes_detalle', $lang, array('slug' => $parking->getSeoSlug()));
//            }
//        }
        
        return $this->render("UtilsBundle:Sitemap:sitemap.xml.twig", array(
            'urls' => $urls, 
            'hostname' => $hostname,
        ));
    }
}