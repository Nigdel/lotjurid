<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="establecimientos")
 * @ORM\Entity
 */
class Establecimientos
{
    /**
     * @var integer $idestab
     *
     * @ORM\Column(name="IdEstab", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var string $licencia
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Lotjuridicas")
     * @ORM\JoinColumn(name="licencia", referencedColumnName="NuLicencia")
     */
    private $licencia;

    /**
     * @var string $nombreestab
     *
     * @ORM\Column(name="NombreEstab", type="string", length=70, nullable=true)
     */
    private $nombreestab;

    /**
     * @var string $lugardeubicacion
     *
     * @ORM\Column(name="LugarDeUbicacion", type="string", length=90, nullable=true)
     */
    private $lugardeubicacion;

    /**
     * @var integer $municipio
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Municipios")
     * @ORM\JoinColumn(name="Municipio", referencedColumnName="ID")
     */
    private $municipio;

    /**
     * @var integer $tpoauxcon
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoAuxCon")
     * @ORM\JoinColumn(name="tpoauxcon", referencedColumnName="id")
     */
    private $tpoauxcon;

    /**
     * @var TipoServAuxCon $tposerv
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoServAuxCon")
     * @ORM\JoinColumn(name="tposerv", referencedColumnName="id")
     */
    private $tposerv;

    /**
     * @var boolean $aseguramiento
     *
     * @ORM\Column(name="Aseguramiento", type="boolean", nullable=true)
     */
    private $aseguramiento;

    /**
     * @var string $numcomp
     *
     * @ORM\Column(name="NumComp", type="string", length=10, nullable=true)
     */
    private $numcomp;

    /**
     * @var \DateTime $fechadeemision
     *
     * @ORM\Column(name="FechaDeEmision", type="date", nullable=true)
     */
    private $fechadeemision;

    /**
     * @var \DateTime $fechaentrega
     *
     * @ORM\Column(name="FechaEntrega", type="date", nullable=true)
     */
    private $fechaentrega;

    /**
     * @var integer $estadoestab
     *
     * @ORM\Column(name="EstadoEstab", type="integer", nullable=true)
     */
    private $estadoestab;

    /**
     * @var boolean $posteado
     *
     * @ORM\Column(name="Posteado", type="boolean", nullable=true)
     */
    private $posteado;

    /**
     * @var boolean $mediatarifa
     *
     * @ORM\Column(name="MediaTarifa", type="boolean", nullable=true)
     */
    private $mediatarifa;

    /**
     * @var integer $anosvigencia
     *
     * @ORM\Column(name="AnosVigencia", type="integer", nullable=true)
     */
    private $anosvigencia;

    /**
     * @var string $medidadeltiempo
     *
     * @ORM\Column(name="MedidaDelTiempo", type="string", length=4, nullable=true)
     */
    private $medidadeltiempo;

    /**
     * @var boolean $duplicado
     *
     * @ORM\Column(name="Duplicado", type="boolean", nullable=true)
     */
    private $duplicado;

    /**
     * @ORM\Column(type="integer")
     */
    private $TipoDeMoneda;

    /**
     * @ORM\Column(name="FechaDeCancelacion",type="date", nullable=true)
     */
    private $FechaDeCancelacion;

    /**
     * @ORM\ManyToOne(targetEntity=CausaCancelacionComp::class)
     * @ORM\Column(name="CausadeCancelacion",nullable=true)
     */
    private $CausadeCancelacion;

    /**
     * @ORM\Column(name="FechaImpresion",type="date", nullable=true)
     */
    private $FechaImpresion;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $Importe;

    /**
     * @ORM\Column(name="SinCosto",type="boolean")
     */
    private $SinCosto;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Establecimientos
     */
    public function setId(int $id): Establecimientos
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLicencia(): string
    {
        return $this->licencia;
    }

    /**
     * @param string $licencia
     * @return Establecimientos
     */
    public function setLicencia(string $licencia): Establecimientos
    {
        $this->licencia = $licencia;
        return $this;
    }

    /**
     * @return string
     */
    public function getNombreestab(): string
    {
        return $this->nombreestab;
    }

    /**
     * @param string $nombreestab
     * @return Establecimientos
     */
    public function setNombreestab(string $nombreestab): Establecimientos
    {
        $this->nombreestab = $nombreestab;
        return $this;
    }

    /**
     * @return string
     */
    public function getLugardeubicacion(): string
    {
        return $this->lugardeubicacion;
    }

    /**
     * @param string $lugardeubicacion
     * @return Establecimientos
     */
    public function setLugardeubicacion(string $lugardeubicacion): Establecimientos
    {
        $this->lugardeubicacion = $lugardeubicacion;
        return $this;
    }

    /**
     * @return Municipios
     */
    public function getMunicipio(): Municipios
    {
        return $this->municipio;
    }

    /**
     * @param int $municipio
     * @return Establecimientos
     */
    public function setMunicipio(int $municipio): Establecimientos
    {
        $this->municipio = $municipio;
        return $this;
    }

    /**
     * @return TipoAuxCon
     */
    public function getTpoauxcon(): TipoAuxCon
    {
        return $this->tpoauxcon;
    }

    /**
     * @param int $tpoauxcon
     * @return Establecimientos
     */
    public function setTpoauxcon(int $tpoauxcon): Establecimientos
    {
        $this->tpoauxcon = $tpoauxcon;
        return $this;
    }

    /**
     * @return TipoServAuxCon
     */
    public function getTposerv(): TipoServAuxCon
    {
        return $this->tposerv;
    }

    /**
     * @param TipoAuxCon $tposerv
     * @return Establecimientos
     */
    public function setTposerv(TipoServAuxCon $tposerv): Establecimientos
    {
        $this->tposerv = $tposerv;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAseguramiento(): bool
    {
        return $this->aseguramiento;
    }

    /**
     * @param bool $aseguramiento
     * @return Establecimientos
     */
    public function setAseguramiento(bool $aseguramiento): Establecimientos
    {
        $this->aseguramiento = $aseguramiento;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumcomp(): string
    {
        return $this->numcomp;
    }

    /**
     * @param string $numcomp
     * @return Establecimientos
     */
    public function setNumcomp(string $numcomp): Establecimientos
    {
        $this->numcomp = $numcomp;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFechadeemision(): \DateTime
    {
        return $this->fechadeemision;
    }

    /**
     * @param \DateTime $fechadeemision
     * @return Establecimientos
     */
    public function setFechadeemision(\DateTime $fechadeemision): Establecimientos
    {
        $this->fechadeemision = $fechadeemision;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFechaentrega(): ?\DateTime
    {
        return $this->fechaentrega;
    }

    /**
     * @param \DateTime $fechaentrega
     * @return Establecimientos
     */
    public function setFechaentrega(\DateTime $fechaentrega): Establecimientos
    {
        $this->fechaentrega = $fechaentrega;
        return $this;
    }

    /**
     * @return int
     */
    public function getEstadoestab(): int
    {
        return $this->estadoestab;
    }

    /**
     * @param int $estadoestab
     * @return Establecimientos
     */
    public function setEstadoestab(int $estadoestab): Establecimientos
    {
        $this->estadoestab = $estadoestab;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPosteado(): bool
    {
        return $this->posteado;
    }

    /**
     * @param bool $posteado
     * @return Establecimientos
     */
    public function setPosteado(bool $posteado): Establecimientos
    {
        $this->posteado = $posteado;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMediatarifa(): bool
    {
        return $this->mediatarifa;
    }

    /**
     * @param bool $mediatarifa
     * @return Establecimientos
     */
    public function setMediatarifa(bool $mediatarifa): Establecimientos
    {
        $this->mediatarifa = $mediatarifa;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnosvigencia(): int
    {
        return $this->anosvigencia;
    }

    /**
     * @param int $anosvigencia
     * @return Establecimientos
     */
    public function setAnosvigencia(int $anosvigencia): Establecimientos
    {
        $this->anosvigencia = $anosvigencia;
        return $this;
    }

    /**
     * @return string
     */
    public function getMedidadeltiempo(): string
    {
        return $this->medidadeltiempo;
    }

    /**
     * @param string $medidadeltiempo
     * @return Establecimientos
     */
    public function setMedidadeltiempo(string $medidadeltiempo): Establecimientos
    {
        $this->medidadeltiempo = $medidadeltiempo;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDuplicado(): bool
    {
        return $this->duplicado;
    }

    /**
     * @param bool $duplicado
     * @return Establecimientos
     */
    public function setDuplicado(bool $duplicado): Establecimientos
    {
        $this->duplicado = $duplicado;
        return $this;
    }

    public function getTipoDeMoneda(): ?int
    {
        return $this->TipoDeMoneda;
    }

    public function setTipoDeMoneda(int $TipoDeMoneda): self
    {
        $this->TipoDeMoneda = $TipoDeMoneda;

        return $this;
    }

    public function getFechaDeCancelacion(): ?\DateTimeInterface
    {
        return $this->FechaDeCancelacion;
    }

    public function setFechaDeCancelacion(?\DateTimeInterface $FechaDeCancelacion): self
    {
        $this->FechaDeCancelacion = $FechaDeCancelacion;

        return $this;
    }

    public function getCausadeCancelacion(): ?CausaCancelacionComp
    {
        return $this->CausadeCancelacion;
    }

    public function setCausadeCancelacion(?CausaCancelacionComp $CausadeCancelacion): self
    {
        $this->CausadeCancelacion = $CausadeCancelacion;

        return $this;
    }

    public function getFechaImpresion(): ?\DateTimeInterface
    {
        return $this->FechaImpresion;
    }

    public function setFechaImpresion(?\DateTimeInterface $FechaImpresion): self
    {
        $this->FechaImpresion = $FechaImpresion;

        return $this;
    }

    public function getImporte(): ?float
    {
        return $this->Importe;
    }

    public function setImporte(?float $Importe): self
    {
        $this->Importe = $Importe;

        return $this;
    }

    public function getSinCosto(): ?bool
    {
        return $this->SinCosto;
    }

    public function setSinCosto(bool $SinCosto): self
    {
        $this->SinCosto = $SinCosto;

        return $this;
    }




}