<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\EstadoLot
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EstadoLot
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombreEstado
     *
     * @ORM\Column(name="nombreEstado", type="string", length=255)
     */
    private $nombreEstado;


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
     * Set nombreEstado
     *
     * @param string $nombreEstado
     * @return EstadoLot
     */
    public function setNombreEstado($nombreEstado)
    {
        $this->nombreEstado = $nombreEstado;
    
        return $this;
    }

    /**
     * Get nombreEstado
     *
     * @return string 
     */
    public function getNombreEstado()
    {
        return $this->nombreEstado;
    }
    public function __toString()
    {
        return $this->getNombreEstado();
    }
}