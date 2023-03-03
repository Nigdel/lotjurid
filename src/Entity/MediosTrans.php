<?php

namespace App\Entity;

use App\Repository\MediosTransRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MediosTransRepository::class)
 */
class MediosTrans
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", length=20)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $basificacion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tipomedio")
     * @ORM\JoinColumn(name="tipomedio", referencedColumnName="IdMedio")
     */
    private $tipoMedio;

    /**
     * @ORM\Column(type="integer")
     */
    private $yearFab;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cap;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Umcap")
     */
    private $umcap;

    /**
     * @ORM\Column(type="boolean")
     */
    private $aseguramiento;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $marca;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $modelo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EstadoMedio")
     */
    private $estadoMedio;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoCombustible")
     * @ORM\JoinColumn(name="tipoCombustible")
     */
    private $tipoCombustible;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $indConsumo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $potencia;

    /**
     * @var integer $tipoPropiedad
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoPropiedad")
     * @ORM\JoinColumn(name="tipoPropiedad")
     */
    private $tipoPropiedad;

    /**
     * @var integer $idrama
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Ramas")
     * @ORM\JoinColumn(name="rama", referencedColumnName="id")
     */
    private $rama;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numRevTecnica;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $vencRevTecnica;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paisAbanderamiento;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idTipo;

    /**
     * @var Basificacion $basificacionObj
     * @ORM\ManyToOne(targetEntity=Basificacion::class, inversedBy="medios")
     * @ORM\JoinColumn(name="basificacionObj", referencedColumnName="IdLBasiAM")
     */
    private $basificacionObj;

    /**
     * @return Basificacion
     */
    public function getBasificacionObj()
    {
        return $this->basificacionObj;
    }

    /**
     * @param mixed $basificacionObj
     * @return MediosTrans
     */
    public function setBasificacionObj($basificacionObj)
    {
        $this->basificacionObj = $basificacionObj;
        return $this;
    }
    /**
     * @ORM\OneToMany(targetEntity=Comprobante::class, mappedBy="medio")
     * @ORM\OrderBy({"femitido" = "ASC"})
     */
    private $comprobantes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $servicio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serviciosEspeciales;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $capPSentados;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $capPDepie;

    /**
     * @ORM\ManyToOne(targetEntity=TipoCamion::class)
     */
    private $tipoCamion;

    public function __construct()
    {
        $this->comprobantes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getBasificacion(): ?string
    {
        return $this->basificacion;
    }

    public function setBasificacion(?string $basificacion): self
    {
        $this->basificacion = $basificacion;

        return $this;
    }

    public function getTipoMedio(): ?Tipomedio
    {
        return $this->tipoMedio;
    }

    public function setTipoMedio(Tipomedio $tipoMedio): self
    {
        $this->tipoMedio = $tipoMedio;

        return $this;
    }

    public function getYearFab(): ?int
    {
        return $this->yearFab;
    }

    public function setYearFab(int $yearFab): self
    {
        $this->yearFab = $yearFab;

        return $this;
    }

    public function getCap(): ?float
    {
        return $this->cap;
    }

    public function setCap(?float $cap): self
    {
        $this->cap = $cap;

        return $this;
    }

    public function getUmcap(): ?Umcap
    {
        return $this->umcap;
    }

    public function setUmcap(?Umcap $umcap): self
    {
        $this->umcap = $umcap;

        return $this;
    }

    public function getAseguramiento(): ?bool
    {
        return $this->aseguramiento;
    }

    public function setAseguramiento(bool $aseguramiento): self
    {
        $this->aseguramiento = $aseguramiento;

        return $this;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(?string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    public function setModelo(?string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function getEstadoMedio(): ? EstadoMedio
    {
        return $this->estadoMedio;
    }

    public function setEstadoMedio(EstadoMedio $estadoMedio): self
    {
        $this->estadoMedio = $estadoMedio;

        return $this;
    }



    public function getTipoCombustible(): ?TipoCombustible
    {
        return $this->tipoCombustible;
    }

    public function setTipoCombustible(TipoCombustible $tipoCombustible): self
    {
        $this->tipoCombustible = $tipoCombustible;

        return $this;
    }

    public function getIndConsumo(): ?float
    {
        return $this->indConsumo;
    }

    public function setIndConsumo(?float $indConsumo): self
    {
        $this->indConsumo = $indConsumo;

        return $this;
    }

    public function getPotencia(): ?int
    {
        return $this->potencia;
    }

    public function setPotencia(?int $potencia): self
    {
        $this->potencia = $potencia;

        return $this;
    }

    public function getTipoPropiedad(): ?TipoPropiedad
    {
        return $this->tipoPropiedad;
    }

    public function setTipoPropiedad(?TipoPropiedad $tipoPropiedad): self
    {
        $this->tipoPropiedad = $tipoPropiedad;

        return $this;
    }

    public function getRama(): ?Ramas
    {
        return $this->rama;
    }

    public function setRama(Ramas $rama): self
    {
        $this->rama = $rama;

        return $this;
    }

    public function getNumRevTecnica(): ?string
    {
        return $this->numRevTecnica;
    }

    public function setNumRevTecnica(?string $numRevTecnica): self
    {
        $this->numRevTecnica = $numRevTecnica;

        return $this;
    }

    public function getVencRevTecnica(): ?\DateTimeInterface
    {
        return $this->vencRevTecnica;
    }

    public function setVencRevTecnica(?\DateTimeInterface $vencRevTecnica): self
    {
        $this->vencRevTecnica = $vencRevTecnica;

        return $this;
    }

    public function getPaisAbanderamiento(): ?string
    {
        return $this->paisAbanderamiento;
    }

    public function setPaisAbanderamiento(?string $paisAbanderamiento): self
    {
        $this->paisAbanderamiento = $paisAbanderamiento;

        return $this;
    }

    public function getIdTipo(): ?int
    {
        return $this->idTipo;
    }

    public function setIdTipo(?int $idTipo): self
    {
        $this->idTipo = $idTipo;

        return $this;
    }

    public  function setId(?string $value){
        $this->id= $value;
        return $this;
    }

    /**
     * @return Collection|Comprobante[]
     */
    public function getComprobantes(): Collection
    {
        return $this->comprobantes;
    }

    public function addComprobante(Comprobante $comprobante): self
    {
        if (!$this->comprobantes->contains($comprobante)) {
            $this->comprobantes[] = $comprobante;
            $comprobante->setMedio($this);
        }

        return $this;
    }

    public function removeComprobante(Comprobante $comprobante): self
    {
        if ($this->comprobantes->contains($comprobante)) {
            $this->comprobantes->removeElement($comprobante);
            // set the owning side to null (unless already changed)
            if ($comprobante->getMedio() === $this) {
                $comprobante->setMedio(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * return Comprobante
     */
    public function getComprobanteActivo() : ?Comprobante
    {

       return $this->comprobantes->last() ? $this->comprobantes->last() : null;
    }

    public function getServicio(): ?string
    {
        if(!$this->servicio){
            $lot=  $this->basificacionObj->getIdlicencia();
            $serv = $lot->getIdtipo()->getTipodelot().' de '.$lot->getServicioamparado().'. '.$this->getRama()->getRamas();
            if($lot->getIdtipo()->getId()== 2){
                $serv = $lot->getServicioamparado().'. '.$this->getRama()->getRamas().'. Limitido a:'.$lot->getLimitacion();
            }
            $this->servicio = $serv;
        }
//        $aseg='';
//        if($this->aseguramiento)
//        $aseg= ". Aseguramiento";
        return $this->servicio;

    }

    public function setServicio(string $servicio): self
    {
        $this->servicio = $servicio;

        return $this;
    }

    public function getServiciosEspeciales(): ?string
    {
        return $this->serviciosEspeciales;
    }

    public function setServiciosEspeciales(?string $serviciosEspeciales): self
    {
        $this->serviciosEspeciales = $serviciosEspeciales;

        return $this;
    }
    public function compActual(): ?Compestab
    {
        $date_compare= function (Compestab $a, Compestab $b){
            $t1 = strtotime($a->getFemitido()->format('Y-m-d'));
            $t2 = strtotime($b->getFemitido()->format('Y-m-d'));
            return $t1 - $t2;
        };
        $comps = $this->getComprobantes()->toArray();
        if($comps != null){
            usort($comps, $date_compare);
            return $comps[count($comps)-1];
        }
        return null;
    }

    public function getCapPSentados(): ?int
    {
        return $this->capPSentados;
    }

    public function setCapPSentados(?int $capPSentados): self
    {
        $this->capPSentados = $capPSentados;

        return $this;
    }

    public function getCapPDepie(): ?int
    {
        return $this->capPDepie;
    }

    public function setCapPDepie(?int $capPDepie): self
    {
        $this->capPDepie = $capPDepie;

        return $this;
    }

    public function getTipoCamion(): ?TipoCamion
    {
        return $this->tipoCamion;
    }

    public function setTipoCamion(?TipoCamion $tipoCamion): self
    {
        $this->tipoCamion = $tipoCamion;

        return $this;
    }

}
