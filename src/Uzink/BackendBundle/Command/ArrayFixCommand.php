<?php
namespace Uzink\BackendBundle\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\ArticleRepository;
use Uzink\BackendBundle\Entity\Category;
use Uzink\BackendBundle\Entity\Message;
use Uzink\BackendBundle\Entity\MessageRepository;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Event\ActivityEvent;

class ArrayFixCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('article:array:fix')
            ->setDescription('Fix arrays')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare('SELECT * FROM article');
        $stmt->execute();
        $articles = $stmt->fetchAll();

        //$value = 'a:1:{s:7:"content";s:8308:"<ul> <li style="text-align:justify">LOS NERVIOS TOR&Aacute;CICOS&nbsp; son&nbsp;los&nbsp;<a class="internal-link" data-internallinks-id="2219" data-internallinks-type="category">NERVIOS ESPINALES&nbsp;</a>&nbsp;&nbsp;que&nbsp; nacen&nbsp; de los segmentos hom&oacute;logos de&nbsp;la&nbsp;<a class="internal-link" data-internallinks-id="2217" data-internallinks-type="category">m&eacute;dula espinal.</a>&nbsp; localizados entre las v&eacute;rtebras s&eacute;ptima &nbsp;<a class="internal-link" data-internallinks-id="2570" data-internallinks-type="article">v&eacute;rtebra cervical</a> y novena <a class="internal-link" data-internallinks-id="2572" data-internallinks-type="article">v&eacute;rtebra&nbsp;tor&aacute;cica</a>&nbsp;</li> <li style="text-align:justify">Hay&nbsp;12 pares de&nbsp; nervios&nbsp; tor&aacute;cicos som&aacute;ticos,&nbsp;designados&nbsp;T1&nbsp;a T12. Cada uno ellos&nbsp;sale por el agujero de conjunci&oacute;n que se encuentra por <strong>debajo</strong> de la v&eacute;rtebra correspondiente a su nivel</li> <li style="text-align:justify">&nbsp;Estos&nbsp;&nbsp;<a class="internal-link" data-internallinks-id="2219" data-internallinks-type="category">NERVIOS ESPINALES&nbsp;</a>&nbsp;presentan 2 zonas claramente diferenciadas:&nbsp;1) &nbsp;<strong>las ra&iacute;ces nerviosas</strong>&nbsp;y&nbsp;2) el&nbsp;<strong>&nbsp;nervio raqu&iacute;deo &nbsp;o &nbsp;espinal propiamente dicho</strong>&nbsp;.&nbsp;Las ra&iacute;ces nerviosas discurren desde cada segmento medular hasta el agujero de conjunci&oacute;n correspondiente para formar el nervio perif&eacute;rico. Las&nbsp;<em>ra&iacute;ces dorsales</em>&nbsp;recogen la informaci&oacute;n sensitiva, encontr&aacute;ndose el soma neuronal en el&nbsp;<em>ganglio raqu&iacute;deo</em>. Las&nbsp;<em>ra&iacute;ces ventrales</em>&nbsp;son motoras, y el soma se localiza en el asta anterior de la m&eacute;dula, desde donde parte el ax&oacute;n por ra&iacute;z anterior y nervio perif&eacute;rico. Distalmente al ganglio raqu&iacute;deo las ra&iacute;ces se unen para formar el nervio espinal o raqu&iacute;deo mixto, que a su vez&nbsp;<em>&nbsp;emite cuatro ramos : men&iacute;ngeo, comunicante, posterior y anterior.&nbsp;</em></li> </ul> <p style="margin-left:80px; text-align:justify"><strong>Figura &nbsp;1.&nbsp; Formaci&oacute;n de los nervios espinales en la regi&oacute;n tor&aacute;cica.&nbsp;&nbsp;</strong>A medida que salen de la m&eacute;dula espinal, las raicillas ventrales y dorsales se unen para formar el ganglio espinal o de ra&iacute;z dorsal. Luego estos ganglios se dividen en rama ventral y dorsal. Las fibras simp&aacute;ticas se originan de las ramas ventrales&nbsp; Antes de la divisi&oacute;n en ramo anterior y posterior, el nervio espinal tor&aacute;cico est&aacute; conectado a la cadena tor&aacute;cica simp&aacute;tica por ramos comunicantes blancos que contienen fibras miel&iacute;nicas preganglionares y aferentes viscerales y ramos comunicantes grises, que contienen fibras post-ganglionares no miel&iacute;nicas.&nbsp;</p> <p style="margin-left:160px; text-align:justify"><img alt="" src="https://dolopedia.com/uploads/media/3-antonio-jose/partes_nervio_espinal.JPG" style="border:0px; box-sizing:border-box; height:250px; max-width:100%; vertical-align:middle; width:400px" />&acirc;&euro;&lsaquo;</p> <p style="margin-left:80px; text-align:justify">&nbsp;</p> <p style="margin-left:80px"><strong>A.- ramo&nbsp;&nbsp;anterior&nbsp;del&nbsp; nervio tor&aacute;cico</strong></p> <ul style="margin-left:80px"> <li>Es un ramo grueso .</li> <li>Este&nbsp; &nbsp;ramo&nbsp; es m&aacute;s largo que el ramo posterior&nbsp;y se distribuye&nbsp;normalmente entre los espacios intercostales, llam&aacute;ndose nervios intercostales. Los nervios intercostales corren entre las costillas, entre los m&uacute;sculos intercostales externos e internos, aunque tambi&eacute;n pueden pasar por encima de la Pleura.</li> <li><strong>Los&nbsp; ramos&nbsp; anteriores de&nbsp; T1-T11&nbsp;</strong>&nbsp;forman los nervios intercostales que discurren a lo largo de los espacios hom&oacute;nimos. NO FORMAN &nbsp;<a class="internal-link" data-internallinks-id="2592" data-internallinks-type="article">PLEXOS &nbsp;NERVIOSOS&nbsp;</a>&nbsp;Adem&aacute;s de los nervios intercostales, tambi&eacute;n dan lugar a los ramos comunicantes, que conectan el nervio intercostal a la cadena simp&aacute;tica.&nbsp;<strong>El ramo anterior del nervio T12</strong>, que discurre inferior a la 12&ordf; costilla, forma el nervio subcostal</li> <li style="text-align:justify">De&nbsp; este&nbsp; ramo&nbsp; anterior&nbsp; surge&nbsp;una rama recurrente, el nervio sinuvertebral de Luschka, que es una divisi&oacute;n del ramo anterior, que se reintroduce por el agujero de conjunci&oacute;n y se anastomosa con los nervios de otros niveles formando un plexo que inerva a las estructuras anteriores del raquis, al ligamento vertebral com&uacute;n posterior, los vasos sangu&iacute;neos del espacio epidural, duramadre anterior, las capas superficiales del anillo fibroso, la vaina dural que rodea las ra&iacute;ces de los nervios espinales, y el periostio vertebral posterior.</li> <li style="text-align:justify">El ramo anterior&nbsp;del nervio tor&aacute;cico&nbsp; &nbsp;es la diana terap&eacute;utica&nbsp; de:1) El&nbsp;&nbsp;<a class="internal-link" data-internallinks-id="3575" data-internallinks-type="category">BLOQUEO DE LOS NERVIOS INTERCOSTALES</a>&nbsp;2) Las&nbsp;&nbsp;<a class="internal-link" data-internallinks-id="743" data-internallinks-type="category">T&Eacute;CNICAS SOBRE RA&Iacute;CES ESPINALES PARA EL ALIVIO DEL DOLOR EN EL T&Oacute;RA</a>&nbsp; y las &nbsp;<a class="internal-link" data-internallinks-id="859" data-internallinks-type="category">T&Eacute;CNICAS SOBRE RA&Iacute;CES ESPINALES PARA EL ALIVIO DEL DOLOR EN EL ABDOMEN</a></li> </ul> <p style="margin-left:80px"><strong>B.-ramo&nbsp;posterior del&nbsp; nervio tor&aacute;cico&nbsp; (o&nbsp; tambi&eacute;n llamado&nbsp; ramo&nbsp; dorsal )&nbsp;&nbsp;</strong></p> <ul style="margin-left:80px"> <li>Es la rama posterior del tronco com&uacute;n&nbsp; y mucho m&aacute;s delgada que el ramo anterior</li> <li style="text-align:justify">A la&nbsp; salida&nbsp; del foramen se dirige hacia atr&aacute;s y&nbsp; se divide&nbsp; en&nbsp; :&nbsp; 1)&nbsp; un&nbsp;<strong>ramo media</strong>l&nbsp; que inerva los&nbsp;&nbsp; m&uacute;sculos&nbsp; y aponeurosis ( mult&iacute;fido&nbsp;&nbsp;&nbsp; y&nbsp; erector espinal&nbsp; )&nbsp;&nbsp; as&iacute;&nbsp; como las articulaciones&nbsp; interapofisarias o&nbsp; facetarias&nbsp;de su nivel y de nivel inferior&nbsp; (&nbsp; ver&nbsp; &nbsp;m&aacute;s&nbsp; en&nbsp; &nbsp; &nbsp;<a class="internal-link" data-internallinks-id="3541" data-internallinks-type="category">ANATOM&Iacute;A DE LA FACETA TOR&Aacute;CICA</a><a class="internal-link" data-internallinks-id="6229" data-internallinks-type="article">&nbsp;: inervaci&oacute;n&nbsp; de la&nbsp; &nbsp;faceta&nbsp;</a>&nbsp;)&nbsp; &nbsp; ;&nbsp; y&nbsp; 2)&nbsp; un&nbsp;<strong>ramo lateral&nbsp;</strong>que inerva&nbsp; &nbsp;1 )&nbsp;&nbsp;las articulaciones costo-vertebrales de su nivel y del inferior&nbsp;,&nbsp; 2)&nbsp; los m&uacute;sculos del dorso de la espalda ; 3&nbsp;) la&nbsp; piel comprendida&nbsp; en la zona tor&aacute;cica&nbsp; y lumbar - la rama cut&aacute;nea de la divisi&oacute;n posterior del nervio T6 inerva la piel de los dermatomas T9 a T10 en su parte posterior, el nervio T10 inerva la piel de la regi&oacute;n de L2 a L3, y el nervio T12 inerva la regi&oacute;n de L5 a S1.</li> </ul> <p style="margin-left:160px; text-align:justify"><strong>Figura&nbsp; 5.&nbsp;&nbsp;</strong>Articulaci&oacute;n facetaria&nbsp; tor&aacute;cica</p> <p style="margin-left:160px; text-align:justify"><img alt="" src="https://dolopedia.com/uploads/media/3-antonio-jose/faceta_toracica.JPG" style="height:400px; text-align:justify; width:745px" /></p> <ul style="margin-left:80px"> <li style="text-align:justify">Esta&nbsp; estructura&nbsp; nerviosa es la diana terap&eacute;utica&nbsp; de:1)&nbsp;&nbsp;&nbsp;<a class="internal-link" data-internallinks-id="3539" data-internallinks-type="category">BLOQUEO DE LAS FACETAS TOR&Aacute;CICAS</a>&nbsp;&nbsp;;</li> </ul>";}';

        /** @var Article $article */
        foreach ($articles as $article) {
            try {
                unserialize($article['content']);
            } catch (\Exception $e) {
                $output->writeln('Broken serialization. Article: ' . $article['id']);
                $fixedContent = preg_replace_callback(
                    '/s:([0-9]+):\"(.*?)\";/',
                    function ($matches) { return "s:".strlen($matches[2]).':"'.$matches[2].'";';     },
                    $article['content']
                );

                $sql = "UPDATE article SET content='{$fixedContent}' WHERE id={$article['id']}";
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->execute();
            }
        }
    }
}