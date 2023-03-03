<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * App\Entity\Lotjuridicas
 *
 * @ORM\Table(name="lotjuridicas")
 * @ORM\Entity(repositoryClass=App\Repository\LotjuridicasRepository::class)
*/
class Lotjuridicas
{
    /**
     * @var string $id
     *
     * @ORM\Column(name="NuLicencia", type="string", length=9)
     * @ORM\Id
     */
    private $id;

    /**
     * @var Personasjuridicas $identidad
     *
     *  @ORM\ManyToOne(targetEntity="App\Entity\Personasjuridicas",inversedBy="lot"  )
     *  @ORM\JoinColumn(name="IdEntidad", referencedColumnName="IdEntidad")
     */
    private $identidad;

    /**
     * @var int $tramitador
     *
     *  @ORM\ManyToOne(targetEntity="App\Entity\User")
     *  @ORM\JoinColumn(name="idtramitador", referencedColumnName="id")
     */
    private $idtramitador;

    /**
     * @var "App\Entity\User" $idaprueba
     *
     *  @ORM\ManyToOne(targetEntity="App\Entity\User")
     *  @ORM\JoinColumn(name="idaprueba", referencedColumnName="id")
     */
    private $idaprueba;

    public function getIdaprueba()
    {
        return $this->idaprueba;
    }

    public function setIdaprueba(User $idaprueba)
    {
        $this->idaprueba = $idaprueba;
        return $this;
    }

    public function getIdtramitador()
    {
        return $this->idtramitador;

    }


    public function setIdtramitador(User $idtramitador)
    {
        $this->idtramitador = $idtramitador;
        return $this;
    }

    /**
     * @var \DateTime $fechasolicitud
     *
     * @ORM\Column(name="FechaSolicitud", type="datetime", nullable=true)
     */
    private $fechasolicitud;

    /**
     * @var boolean $presentada
     *
     * @ORM\Column(name="Presentada", type="boolean", nullable=true)
     */
    private $presentada;

    /**
     * @var boolean $aprobada
     *
     * @ORM\Column(name="Aprobada", type="boolean", nullable=true)
     */
    private $aprobada;

    /**
     * @var \DateTime $fechaaprobacion
     *
     * @ORM\Column(name="FechaAprobacion", type="datetime", nullable=true)
     */
    private $fechaaprobacion;

    /**
     * @var \DateTime $fechaemision
     *
     * @ORM\Column(name="FechaEmision", type="datetime", nullable=true)
     */
    private $fechaemision;

    /**
     * @var \DateTime $fechaentrega
     *
     * @ORM\Column(name="FechaEntrega", type="datetime", nullable=true)
     * @Assert\Type(type="\DateTime")
     */
    private $fechaentrega;

    /**
     * @var TipoLot $idtipo
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoLot")
     * @ORM\JoinColumn(name="IDTipo", referencedColumnName="id")
     */
    private $idtipo;

    /**
     * @var TipoServicio $idservicio
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoServicio")
     * @ORM\JoinColumn(name="IDServicio", referencedColumnName="id")
     */
    private $idservicio;

    /**
     * @var ServicioAmparado $servicioamparado
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ServicioAmparado")
     * @ORM\JoinColumn(name="ServicioAmparado", referencedColumnName="id")
     */
    private $servicioamparado;

    /**
     * @var string $limitacion
     *
     * @ORM\Column(name="limitacion", type="string", nullable=true)
     */
    private $limitacion;

    /**
     * @var integer $tpomedioamparado
     *
     * @ORM\Column(name="TpoMedioAmparado", type="integer", nullable=true)
     */
    private $tpomedioamparado;

    /**
     * @var integer $duracion
     *
     * @ORM\Column(name="Duracion", type="integer", nullable=true)
     *  @Assert\Range(min=0, minMessage = "Debe ser al >= 0")
     */
    private $duracion;

    /**
     * @var Extension $idextension
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Extension")
     * @ORM\JoinColumn(name="idextension", referencedColumnName="id")
     */
    private $idextension;

    /**
     * @var Ramas $idrama
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Ramas")
     * @ORM\JoinColumn(name="idrama", referencedColumnName="id")
     */
    private $idrama;

     /**
     * @ORM\OneToMany(targetEntity=Comprobante::class, mappedBy="lot")
     * @ORM\OrderBy({"femitido" = "ASC"})
     */
    private $comprobantes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Basificacion", mappedBy="idlicencia")
     * @ORM\OrderBy({"nombrelb" = "ASC"})
     */
    private $basificaciones;

    /**
     * @return mixed
     */
    public function getBasificaciones()
    {
        return $this->basificaciones;
    }

    /**
     * @param mixed $basificaciones
     * @return Lotjuridicas
     */
    public function setBasificaciones($basificaciones)
    {
        $this->basificaciones = $basificaciones;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getComprobantes()
    {
        return $this->comprobantes;
    }

    /**
     * @param mixed $comprobantes
     * @return Lotjuridicas
     */
    public function setComprobantes($comprobantes)
    {
        $this->comprobantes = $comprobantes;
        return $this;
    }

    /**
     * @var \DateTime $fechadecancelacion
     *
     * @ORM\Column(name="FechaDeCancelacion", type="datetime", nullable=true)
     */
    private $fechadecancelacion;

    /**
     * @var \DateTime $fecharenov
     *
     * @ORM\Column(name="FechaRenov", type="datetime", nullable=true)
     *
     */
    private $fecharenov;

    /**
     * @return \DateTime
     */
    public function getFecharenov(): ?\DateTime
    {
        return $this->fecharenov;
    }

    /**
     * @param \DateTime $fecharenov
     * @return Lotjuridicas
     */
    public function setFecharenov(?\DateTime $fecharenov): Lotjuridicas
    {
        $this->fecharenov = $fecharenov;
        return $this;
    }

    /**
     * @var EstadoLot $idestado
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EstadoLot")
     * @ORM\JoinColumn(name="IDEstado", referencedColumnName="id")
     */
    private $idestado;

    /**
     * @var boolean $posteada
     *
     * @ORM\Column(name="Posteada", type="boolean", nullable=true)
     */
    private $posteada;

    /**
     * @var boolean $mediatarifa
     *
     * @ORM\Column(name="MediaTarifa", type="boolean", nullable=true)
     */
    private $mediatarifa;

    /**
     * @var string $numfolio
     *
     * @ORM\Column(name="NumFolio", type="string", length=10, nullable=true)
     */
    private $numfolio;

    /**
     * @var \DateTime $fechaaprobinicial
     *
     * @ORM\Column(name="FechaAprobInicial", type="datetime", nullable=true)
     */
    private $fechaaprobinicial;

    /**
     * @var integer $tiposolicitud
     *
     * @ORM\Column(name="TipoSolicitud", type="integer", nullable=true)
     */
    private $tiposolicitud;

    /**
     * @var CausaCancelacionLot $causadecancelacion
     *
     * @ORM\ManyToOne(targetEntity= "App\Entity\CausaCancelacionLot")
     * @ORM\JoinColumn(nullable=true)
     */
    private $causadecancelacion;

    /**
     * @var string $dictamen
     *
     * @ORM\Column(name="Dictamen", type="text", nullable=true)
     */
    private $dictamen;

    /**
     * @var string $c_negacion
     * @ORM\Column(name="c_negacion", type="text", nullable=true)
     */
    private $c_negacion;

    /**
     * @return string
     */
    public function getCNegacion()
    {
        return $this->c_negacion;
    }

    /**
     * @param string $c_negacion
     * @return Lotjuridicas
     */
    public function setCNegacion($c_negacion)
    {
        $this->c_negacion = $c_negacion;
        return $this;
    }


    /**
     * @var integer $prorrogadoendias
     *
     * @ORM\Column(name="ProrrogadoEnDias", type="smallint", nullable=true)
     */
    private $prorrogadoendias;

    /**
     * @var \DateTime $fechadedestruccion
     *
     * @ORM\Column(name="FechaDeDestruccion", type="datetime", nullable=true)
     */
    private $fechadedestruccion;

    /**
     * @var boolean $duplicado
     *
     * @ORM\Column(name="Duplicado", type="boolean", nullable=true)
     */
    private $duplicado;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fimpresion;

    /**
     * @ORM\OneToMany(targetEntity=Instalaciones::class, mappedBy="lot", orphanRemoval=true)
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $instalaciones;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaSuspension;

    /**
     * @ORM\ManyToOne(targetEntity=CausaSuspensionLot::class)
     */
    private $causaSuspension;

    /**
     * @ORM\OneToMany(targetEntity=Tramite::class, mappedBy="lot", orphanRemoval=true)
     */
    private $tramites;

    /**
     * @ORM\Column(type="float")
     */
    private $importe;

//    /**
//     * @ORM\ManyToOne(targetEntity=Personasjuridicas::class, inversedBy="lot")
//     */
//    private $personasjuridicas;



    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
       $this->id = $id;
       return $this;
    }
    /**
     * Set identidad
     *
     * @param Personasjuridicas $identidad
     * @return Lotjuridicas
     */
    public function setIdentidad($identidad)
    {
        $this->identidad = $identidad;
    
        return $this;
    }

    /**
     * Get identidad
     *
     * @return Personasjuridicas
     */
    public function getIdentidad()
    {
        return $this->identidad;
    }

    /**
     * Set fechasolicitud
     *
     * @param \DateTime $fechasolicitud
     * @return Lotjuridicas
     */
    public function setFechasolicitud($fechasolicitud)
    {
        $this->fechasolicitud = $fechasolicitud;
    
        return $this;
    }

    /**
     * Get fechasolicitud
     *
     * @return \DateTime 
     */
    public function getFechasolicitud()
    {
        return $this->fechasolicitud;
    }

    /**
     * Set presentada
     *
     * @param boolean $presentada
     * @return Lotjuridicas
     */
    public function setPresentada($presentada)
    {
        $this->presentada = $presentada;
    
        return $this;
    }

    /**
     * Get presentada
     *
     * @return boolean 
     */
    public function getPresentada()
    {
        return $this->presentada;
    }

    /**
     * Set aprobada
     *
     * @param boolean $aprobada
     * @param string $causa
     * @return Lotjuridicas
     */
    public function setAprobada($aprobada, $causa=null)
    {
        $this->aprobada = $aprobada;
        $this->c_negacion= $causa;
    
        return $this;
    }

    /**
     * Get aprobada
     *
     * @return boolean 
     */
    public function getAprobada()
    {
        return $this->aprobada;
    }

    /**
     * Set fechaaprobacion
     *
     * @param \DateTime $fechaaprobacion
     * @return Lotjuridicas
     */
    public function setFechaaprobacion($fechaaprobacion)
    {
        $this->fechaaprobacion = $fechaaprobacion;
    
        return $this;
    }

    /**
     * Get fechaaprobacion
     *
     * @return \DateTime 
     */
    public function getFechaaprobacion()
    {
        return $this->fechaaprobacion;
    }

    /**
     * Set fechaemision
     *
     * @param \DateTime $fechaemision
     * @return Lotjuridicas
     */
    public function setFechaemision($fechaemision)
    {
        $this->fechaemision = $fechaemision;
    
        return $this;
    }

    /**
     * Get fechaemision
     *
     * @return \DateTime 
     */
    public function getFechaemision()
    {
        return $this->fechaemision;
    }

    /**
     * Set fechaentrega
     *
     * @param \DateTime $fechaentrega
     * @return Lotjuridicas
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
     * Set idtipo
     *
     * @param integer $idtipo
     * @return Lotjuridicas
     */
    public function setIdtipo($idtipo)
    {
        $this->idtipo = $idtipo;
    
        return $this;
    }

    /**
     * Get idtipo
     *
     * @return TipoLot
     */
    public function getIdtipo()
    {
        return $this->idtipo;
    }

    /**
     * Set idservicio
     *
     * @param TipoServicio $idservicio
     * @return Lotjuridicas
     */
    public function setIdservicio($idservicio)
    {
        $this->idservicio = $idservicio;
    
        return $this;
    }

    /**
     * Get idservicio
     *
     * @return TipoServicio
     */
    public function getIdservicio()
    {
        return $this->idservicio;
    }

    /**
     * Set servicioamparado
     *
     * @param ServicioAmparado $servicioamparado
     * @return Lotjuridicas
     */
    public function setServicioamparado($servicioamparado)
    {
        $this->servicioamparado = $servicioamparado;
    
        return $this;
    }

    /**
     * Get servicioamparado
     *
     * @return ServicioAmparado
     */
    public function getServicioamparado()
    {
        return $this->servicioamparado;
    }

    /**
     * Set limitacion
     *
     * @param string $limitacion
     * @return Lotjuridicas
     */
    public function setLimitacion($limitacion)
    {
        $this->limitacion = $limitacion;
    
        return $this;
    }

    /**
     * Get limitacion
     *
     * @return string
     */
    public function getLimitacion()
    {
        return $this->limitacion;
    }

    /**
     * Set tpomedioamparado
     *
     * @param integer $tpomedioamparado
     * @return Lotjuridicas
     */
    public function setTpomedioamparado($tpomedioamparado)
    {
        $this->tpomedioamparado = $tpomedioamparado;
    
        return $this;
    }

    /**
     * Get tpomedioamparado
     *
     * @return integer 
     */
    public function getTpomedioamparado()
    {
        return $this->tpomedioamparado;
    }

    /**
     * Set duracion
     *
     * @param integer $duracion
     * @return Lotjuridicas
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;
    
        return $this;
    }

    /**
     * Get duracion
     *
     * @return integer 
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Set idextension
     *
     * @param integer $idextension
     * @return Lotjuridicas
     */
    public function setIdextension($idextension)
    {
        $this->idextension = $idextension;
    
        return $this;
    }

    /**
     * Get idextension
     *
     * @return Extension
     */
    public function getIdextension()
    {
        return $this->idextension;
    }

    /**
     * Set idrama
     *
     * @param Ramas $idrama
     * @return Lotjuridicas
     */
    public function setIdrama($idrama)
    {
        $this->idrama = $idrama;
    
        return $this;
    }

    /**
     * Get idrama
     *
     * @return Ramas
     */
    public function getIdrama()
    {
        return $this->idrama;
    }



    /**
     * Set fechadecancelacion
     *
     * @param \DateTime $fechadecancelacion
     * @return Lotjuridicas
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
     * Set idestado
     *
     * @param EstadoLot
     * @return Lotjuridicas
     */
    public function setIdestado($idestado)
    {
        $this->idestado = $idestado;
    
        return $this;
    }

    /**
     * Get idestado
     *
     * @return EstadoLot
     */
    public function getIdestado()
    {
        return $this->idestado;
    }

    /**
     * Set posteada
     *
     * @param boolean $posteada
     * @return Lotjuridicas
     */
    public function setPosteada($posteada)
    {
        $this->posteada = $posteada;
    
        return $this;
    }

    /**
     * Get posteada
     *
     * @return boolean 
     */
    public function getPosteada()
    {
        return $this->posteada;
    }

    /**
     * Set mediatarifa
     *
     * @param boolean $mediatarifa
     * @return Lotjuridicas
     */
    public function setMediatarifa($mediatarifa)
    {
        $this->mediatarifa = $mediatarifa;
    
        return $this;
    }

    /**
     * Get mediatarifa
     *
     * @return boolean 
     */
    public function getMediatarifa()
    {
        return $this->mediatarifa;
    }


    /**
     * Set anosvigencia
     *
     * @param integer $anosvigencia
     * @return Lotjuridicas

    public function setAnosvigencia($anosvigencia)
    {
        $this->anosvigencia = $anosvigencia;
    
        return $this;
    }*/

    /**
     * Get anosvigencia
     *
     * @return integer 

    public function getAnosvigencia()
    {
        return $this->anosvigencia;
    }*/

    /**
     * Set numfolio
     *
     * @param string $numfolio
     * @return Lotjuridicas
     */
    public function setNumfolio($numfolio)
    {
        $this->numfolio = $numfolio;
    
        return $this;
    }

    /**
     * Get numfolio
     *
     * @return string 
     */
    public function getNumfolio()
    {
        return $this->numfolio;
    }

    /**
     * Set fechaaprobinicial
     *
     * @param \DateTime $fechaaprobinicial
     * @return Lotjuridicas
     */
    public function setFechaaprobinicial($fechaaprobinicial)
    {
        $this->fechaaprobinicial = $fechaaprobinicial;
    
        return $this;
    }

    /**
     * Get fechaaprobinicial
     *
     * @return \DateTime 
     */
    public function getFechaaprobinicial()
    {
        return $this->fechaaprobinicial;
    }

    /**
     * Set tiposolicitud
     *
     * @param integer $tiposolicitud
     * @return Lotjuridicas
     */
    public function setTiposolicitud($tiposolicitud)
    {
        $this->tiposolicitud = $tiposolicitud;
    
        return $this;
    }

    /**
     * Get tiposolicitud
     *
     * @return integer 
     */
    public function getTiposolicitud()
    {
        return $this->tiposolicitud;
    }

    /**
     * Set causadecancelacion
     *
     * @param CausaCancelacionLot $causadecancelacion
     * @return Lotjuridicas
     */
    public function setCausadecancelacion(?CausaCancelacionLot $causadecancelacion)
    {
        $this->causadecancelacion = $causadecancelacion;
    
        return $this;
    }

    /**
     * Get causadecancelacion
     *
     * @return CausaCancelacionLot
     */
    public function getCausadecancelacion() : ?CausaCancelacionLot
    {
        return $this->causadecancelacion;
    }

    /**
     * Set dictamen
     *
     * @param string $dictamen
     * @return Lotjuridicas
     */
    public function setDictamen($dictamen)
    {
        $this->dictamen = $dictamen;
    
        return $this;
    }

    /**
     * Get dictamen
     *
     * @return string 
     */
    public function getDictamen()
    {
        return $this->dictamen;
    }

    /**
     * Set prorrogadoendias
     *
     * @param integer $prorrogadoendias
     * @return Lotjuridicas
     */
    public function setProrrogadoendias($prorrogadoendias)
    {
        $this->prorrogadoendias = $prorrogadoendias;
    
        return $this;
    }

    /**
     * Get prorrogadoendias
     *
     * @return integer 
     */
    public function getProrrogadoendias()
    {
        return $this->prorrogadoendias;
    }

    /**
     * Set fechadedestruccion
     *
     * @param \DateTime $fechadedestruccion
     * @return Lotjuridicas
     */
    public function setFechadedestruccion($fechadedestruccion)
    {
        $this->fechadedestruccion = $fechadedestruccion;
    
        return $this;
    }

    /**
     * Get fechadedestruccion
     *
     * @return \DateTime 
     */
    public function getFechadedestruccion()
    {
        return $this->fechadedestruccion;
    }

    /**
     * Set duplicado
     *
     * @param boolean $duplicado
     * @return Lotjuridicas
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

    public function __construct()
    {

        $this->setAprobada(false,"Sin Presentar");
        $this->comprobantes = new ArrayCollection();
        $this->basificaciones = new ArrayCollection();
        $this->fechasolicitud = new \DateTime('today');
        $this->instalaciones = new ArrayCollection();
        $this->tramites = new ArrayCollection();

    }
    public function __toString()
    {
        return $this->getId();
    }
    public function addComprobante(Comprobante $comprobante): self
    {
        if (!$this->comprobantes->contains($comprobante)) {
            $this->comprobantes[] = $comprobante;
            $comprobante->setLot($this);
        }

        return $this;
    }

    public function removeComprobante(Comprobante $comprobante): self
    {
        if ($this->comprobantes->contains($comprobante)) {
            $this->comprobantes->removeElement($comprobante);
            // set the owning side to null (unless already changed)
            if ($comprobante->getLot() === $this) {
                $comprobante->setLot(null);
            }
        }

        return $this;
    }

    public function addBasificacion(Basificacion $basif): self
    {
        if (!$this->basificaciones->contains($basif)) {
            $this->basificaciones[] = $basif;
            $basif->setIdlicencia($this);
        }

        return $this;
    }

    public function removeBasificacion(Basificacion $basif): self
    {
        if ($this->basificaciones->contains($basif)) {
            $this->basificaciones->removeElement($basif);
            // set the owning side to null (unless already changed)
            if ($basif->getIdlicencia() === $this) {
                $basif->setIdlicencia(null);
            }
        }

        return $this;
    }

    public function addBasificacione(Basificacion $basificacione): self
    {
        if (!$this->basificaciones->contains($basificacione)) {
            $this->basificaciones[] = $basificacione;
            $basificacione->setIdlicencia($this);
        }

        return $this;
    }

    public function removeBasificacione(Basificacion $basificacione): self
    {
        if ($this->basificaciones->contains($basificacione)) {
            $this->basificaciones->removeElement($basificacione);
            // set the owning side to null (unless already changed)
            if ($basificacione->getIdlicencia() === $this) {
                $basificacione->setIdlicencia(null);
            }
        }

        return $this;
    }


    public function getFimpresion(): ?\DateTimeInterface
    {
        return $this->fimpresion;
    }

    public function setFimpresion(?\DateTimeInterface $fimpresion): self
    {
        $this->fimpresion = $fimpresion;

        return $this;
    }

    /**
     * @return Collection|Instalaciones[]
     */
    public function getInstalaciones(): Collection
    {
        return $this->instalaciones;
    }

    public function addInstalacione(Instalaciones $instalacione): self
    {
        if (!$this->instalaciones->contains($instalacione)) {
            $this->instalaciones[] = $instalacione;
            $instalacione->setLot($this);
        }

        return $this;
    }

    public function removeInstalacione(Instalaciones $instalacione): self
    {
        if ($this->instalaciones->contains($instalacione)) {
            $this->instalaciones->removeElement($instalacione);
            // set the owning side to null (unless already changed)
            if ($instalacione->getLot() === $this) {
                $instalacione->setLot(null);
            }
        }

        return $this;
    }

    public function getFechaSuspension(): ?\DateTimeInterface
    {
        return $this->fechaSuspension;
    }

    public function setFechaSuspension(?\DateTimeInterface $fechaSuspension): self
    {
        $this->fechaSuspension = $fechaSuspension;

        return $this;
    }

    public function getCausaSuspension(): ?CausaSuspensionLot
    {
        return $this->causaSuspension;
    }

    public function setCausaSuspension(?CausaSuspensionLot $causaSuspension): self
    {
        $this->causaSuspension = $causaSuspension;

        return $this;
    }
    public function Renovar(EstadoLot $pteAprob){
        $this->setAprobada(false,"Sin Presentar");
        $this->setFechaaprobacion(null);
        $this->setFechaentrega(null);
        $this->setFecharenov(null);
        $this->fechaemision = null;
        $this->fechasolicitud = null;
        $this->setPresentada(false);
        $this->setMediatarifa(false);
        $this->setDuplicado(false);
        $this->setNumfolio(null);
        $this->setIdestado($pteAprob);
        return $this;
    }
    public function Duplicar(EstadoLot $pteImpress){

        $this->setNumfolio(null);
        $this->setIdestado($pteImpress);
        $this->setFechaentrega(null);
        $this->setFimpresion(null);
        $this->setDuplicado(true);
        return $this;
    }

    /**
     * @return Collection|Tramite[]
     */
    public function getTramites(): Collection
    {
        return $this->tramites;
    }

    public function addTramite(Tramite $tramite): self
    {
        if (!$this->tramites->contains($tramite)) {
            $this->tramites[] = $tramite;
            $tramite->setLot($this);
        }

        return $this;
    }

    public function removeTramite(Tramite $tramite): self
    {
        if ($this->tramites->contains($tramite)) {
            $this->tramites->removeElement($tramite);
            // set the owning side to null (unless already changed)
            if ($tramite->getLot() === $this) {
                $tramite->setLot(null);
            }
        }

        return $this;
    }

    public function getImporte(): ?float
    {
        return $this->importe;
    }

    public function setImporte(float $importe): self
    {
        $this->importe = $importe;

        return $this;
    }
}