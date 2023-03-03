<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\TipoServicio
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TipoServicio
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
     * @var string $TipoServicio
     *
     * @ORM\Column(name="TipoServicio", type="string", length=20)
     */
    private $TipoServicio;


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
     * Set TipoServicio
     *
     * @param string $tipoServicio
     * @return TipoServicio
     */
    public function setTipoServicio($tipoServicio)
    {
        $this->TipoServicio = $tipoServicio;
    
        return $this;
    }

    /**
     * Get TipoServicio
     *
     * @return string 
     */
    public function getTipoServicio()
    {
        return $this->TipoServicio;
    }

    public function __toString()
    {
        return $this->getTipoServicio();
    }

}