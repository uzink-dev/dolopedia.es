<?php

namespace Uzink\ConfiguracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ParametroConfiguracion
 *
 * @ORM\Table("parametro_configuracion")
 * @ORM\Entity(repositoryClass="Uzink\ConfiguracionBundle\Entity\ParametroConfiguracionRepository")
 */
class ParametroConfiguracion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", unique=true, nullable=false, length=255)
     * @Assert\NotBlank(message = "Debe introducir un nombre")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=255)
     * @Assert\NotBlank(message = "Debe introducir un valor")
     */
    private $valor;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return ParametroConfiguracion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return ParametroConfiguracion
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    
        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }
}
