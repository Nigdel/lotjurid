<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Foliocomprobantes
 *
 * @ORM\Table(name="foliocomprobantes")
 * @ORM\Entity
 */
class Foliocomprobantes
{
    /**
     * @var string $folio
     *
     * @ORM\Column(name="Folio", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $folio;

    /**
     * @var string $licencia
     *
     * @ORM\Column(name="Licencia", type="string", length=9, nullable=true)
     */
    private $licencia;

    /**
     * @var string $chapa
     *
     * @ORM\Column(name="Chapa", type="string", length=9, nullable=true)
     */
    private $chapa;

    /**
     * @var string $colortipo
     *
     * @ORM\Column(name="ColorTipo", type="string", length=2, nullable=true)
     */
    private $colortipo;

    /**
     * @var \DateTime $fechaemitido
     *
     * @ORM\Column(name="FechaEmitido", type="datetime", nullable=true)
     */
    private $fechaemitido;

    /**
     * @var \DateTime $fechaentrega
     *
     * @ORM\Column(name="FechaEntrega", type="datetime", nullable=true)
     */
    private $fechaentrega;

    /**
     * @var integer $estadomodelolot
     *
     * @ORM\Column(name="EstadoModeloLot", type="smallint", nullable=true)
     */
    private $estadomodelolot;

    /**
     * @var \DateTime $fechadecancelacion
     *
     * @ORM\Column(name="FechaDeCancelacion", type="datetime", nullable=true)
     */
    private $fechadecancelacion;

    /**
     * @var integer $causadecancelacion
     *
     * @ORM\Column(name="CausadeCancelacion", type="smallint", nullable=true)
     */
    private $causadecancelacion;

    /**
     * @var \DateTime $fechaimpresion
     *
     * @ORM\Column(name="FechaImpresion", type="datetime", nullable=true)
     */
    private $fechaimpresion;

    /**
     * @var boolean $sincosto
     *
     * @ORM\Column(name="SinCosto", type="boolean", nullable=true)
     */
    private $sincosto;

    /**
     * @var boolean $duplicado
     *
     * @ORM\Column(name="Duplicado", type="boolean", nullable=true)
     */
    private $duplicado;

    /**
     * @var integer $moneda
     *
     * @ORM\Column(name="Moneda", type="integer", nullable=true)
     */
    private $moneda;

    /**
     * @var integer $extension
     *
     * @ORM\Column(name="Extension", type="smallint", nullable=true)
     */
    private $extension;

    /**
     * @var float $importe
     *
     * @ORM\Column(name="Importe", type="float", nullable=true)
     */
    private $importe;



    /**
     * Get folio
     *
     * @return string 
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * Set licencia
     *
     * @param string $licencia
     * @return Foliocomprobantes
     */
    public function setLicencia($licencia)
    {
        $this->licencia = $licencia;
    
        return $this;
    }

    /**
     * Get licencia
     *
     * @return string 
     */
    public function getLicencia()
    {
        return $this->licencia;
    }

    /**
     * Set chapa
     *
     * @param string $chapa
     * @return Foliocomprobantes
     */
    public function setChapa($chapa)
    {
        $this->chapa = $chapa;
    
        return $this;
    }

    /**
     * Get chapa
     *
     * @return string 
     */
    public function getChapa()
    {
        return $this->chapa;
    }

    /**
     * Set colortipo
     *
     * @param string $colortipo
     * @return Foliocomprobantes
     */
    public function setColortipo($colortipo)
    {
        $this->colortipo = $colortipo;
    
        return $this;
    }

    /**
     * Get colortipo
     *
     * @return string 
     */
    public function getColortipo()
    {
        return $this->colortipo;
    }

    /**
     * Set fechaemitido
     *
     * @param \DateTime $fechaemitido
     * @return Foliocomprobantes
     */
    public function setFechaemitido($fechaemitido)
    {
        $this->fechaemitido = $fechaemitido;
    
        return $this;
    }

    /**
     * Get fechaemitido
     *
     * @return \DateTime 
     */
    public function getFechaemitido()
    {
        return $this->fechaemitido;
    }

    /**
     * Set fechaentrega
     *
     * @param \DateTime $fechaentrega
     * @return Foliocomprobantes
     */
    public function setFechaentrega($fechaentrega)
    {
        $this->fechaentrega = $fechaentrega;
    
        return $this;
    }

    /**
     * Get fechaentrega
     *
     * @return \DateTime 
     */
    public function getFechaentrega()
    {
        return $this->fechaentrega;
    }

    /**
     * Set estadomodelolot
     *
     * @param integer $estadomodelolot
     * @return Foliocomprobantes
     */
    public function setEstadomodelolot($estadomodelolot)
    {
        $this->estadomodelolot = $estadomodelolot;
    
        return $this;
    }

    /**
     * Get estadomodelolot
     *
     * @return integer 
     */
    public function getEstadomodelolot()
    {
        return $this->estadomodelolot;
    }

    /**
     * Set fechadecancelacion
     *
     * @param \DateTime $fechadecancelacion
     * @return Foliocomprobantes
     */
    public function setFechadecancelacion($fechadecancelacion)
    {
        $this->fechadecancelacion = $fechadecancelacion;
    
        return $this;
    }

    /**
     * Get fechadecancelacion
     *
     * @return \DateTime 
     */
    public function getFechadecancelacion()
    {
        return $this->fechadecancelacion;
    }

    /**
     * Set causadecancelacion
     *
     * @param integer $causadecancelacion
     * @return Foliocomprobantes
     */
    public function setCausadecancelacion($causadecancelacion)
    {
        $this->causadecancelacion = $causadecancelacion;
    
        return $this;
    }

    /**
     * Get causadecancelacion
     *
     * @return integer 
     */
    public function getCausadecancelacion()
    {
        return $this->causadecancelacion;
    }

    /**
     * Set fechaimpresion
     *
     * @param \DateTime $fechaimpresion
     * @return Foliocomprobantes
     */
    public function setFechaimpresion($fechaimpresion)
    {
        $this->fechaimpresion = $fechaimpresion;
    
        return $this;
    }

    /**
     * Get fechaimpresion
     *
     * @return \DateTime 
     */
    public function getFechaimpresion()
    {
        return $this->fechaimpresion;
    }

    /**
     * Set sincosto
     *
     * @param boolean $sincosto
     * @return Foliocomprobantes
     */
    public function setSincosto($sincosto)
    {
        $this->sincosto = $sincosto;
    
        return $this;
    }

    /**
     * Get sincosto
     *
     * @return boolean 
     */
    public function getSincosto()
    {
        return $this->sincosto;
    }

    /**
     * Set duplicado
     *
     * @param boolean $duplicado
     * @return Foliocomprobantes
     */
    public function setDuplicado($duplicado)
    {
        $this->duplicado = $duplicado;
    
        return $this;
    }

    /**
     * Get duplicado
     *
     * @return boolean 
     */
    public function getDuplicado()
    {
        return $this->duplicado;
    }

    /**
     * Set moneda
     *
     * @param integer $moneda
     * @return Foliocomprobantes
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    
        return $this;
    }

    /**
     * Get moneda
     *
     * @return integer 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set extension
     *
     * @param integer $extension
     * @return Foliocomprobantes
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    
        return $this;
    }

    /**
     * Get extension
     *
     * @return integer 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set importe
     *
     * @param float $importe
     * @return Foliocomprobantes
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    
        return $this;
    }

    /**
     * Get importe
     *
     * @return float 
     */
    public function getImporte()
    {
        return $this->importe;
    }
}