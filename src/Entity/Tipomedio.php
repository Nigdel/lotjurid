<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Tipomedio
 *
 * @ORM\Table(name="tipomedio")
 * @ORM\Entity
 */
class Tipomedio
{
    /**
     * @var integer $idmedio
     *
     * @ORM\Column(name="IdMedio", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $idmedio;

    /**
     * @var string $medios
     *
     * @ORM\Column(name="Medios", type="string", length=50, nullable=true)
     */
    private $medios;

    /**
     * @var integer $rama
     *
     * @ORM\Column(name="Rama", type="integer", nullable=true)
     */
    private $rama;

    /**
     * @var integer $tipoanexo1
     *
     * @ORM\Column(name="TipoAnexo1", type="integer", nullable=true)
     */
    private $tipoanexo1;

    /**
     * @var integer $tipoparteoperativo
     *
     * @ORM\Column(name="TipoParteOperativo", type="integer", nullable=true)
     */
    private $tipoparteoperativo;



    /**
     * Get idmedio
     *
     * @return integer 
     */
    public function getIdmedio()
    {
        return $this->idmedio;
    }

    /**
     * Set medios
     *
     * @param string $medios
     * @return Tipomedio
     */
    public function setMedios($medios)
    {
        $this->medios = $medios;
    
        return $this;
    }

    /**
     * Get medios
     *
     * @return string 
     */
    public function getMedios()
    {
        return $this->medios;
    }

    /**
     * Set rama
     *
     * @param integer $rama
     * @return Tipomedio
     */
    public function setRama($rama)
    {
        $this->rama = $rama;
    
        return $this;
    }

    /**
     * Get rama
     *
     * @return integer 
     */
    public function getRama()
    {
        return $this->rama;
    }

    /**
     * Set tipoanexo1
     *
     * @param integer $tipoanexo1
     * @return Tipomedio
     */
    public function setTipoanexo1($tipoanexo1)
    {
        $this->tipoanexo1 = $tipoanexo1;
    
        return $this;
    }

    /**
     * Get tipoanexo1
     *
     * @return integer 
     */
    public function getTipoanexo1()
    {
        return $this->tipoanexo1;
    }

    /**
     * Set tipoparteoperativo
     *
     * @param integer $tipoparteoperativo
     * @return Tipomedio
     */
    public function setTipoparteoperativo($tipoparteoperativo)
    {
        $this->tipoparteoperativo = $tipoparteoperativo;
    
        return $this;
    }

    /**
     * Get tipoparteoperativo
     *
     * @return integer 
     */
    public function getTipoparteoperativo()
    {
        return $this->tipoparteoperativo;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMedios();
    }


}