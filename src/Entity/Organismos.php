<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Organismos
 *
 * @ORM\Table(name="organismos")
 * @ORM\Entity
 */
class Organismos
{
    /**
     * @var integer $Cod
     *
     * @ORM\Column(name="Cod", type="integer", nullable=false)
     * @ORM\Id
     */
    private $Cod;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="Descripcion", type="string", length=200, nullable=true)
     */
    private $descripcion;

    /**
     * @var string $desc
     *
     * @ORM\Column(name="Desc", type="string", length=200, nullable=true)
     */
    private $desc;



    /**
     * Get cod
     *
     * @return integer 
     */
    public function getCod()
    {
        return $this->Cod;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Organismos
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return Organismos
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    
        return $this;
    }

    /**
     * Get desc
     *
     * @return string 
     */
    public function getDesc()
    {
        return $this->desc;
    }

    public function __toString(){
        if(!$this)
        return "No válido";
        return $this->descripcion."(".$this->desc.")";
    }

}