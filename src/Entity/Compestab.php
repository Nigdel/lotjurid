<?php

namespace App\Entity;

use App\Repository\CompestabRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompestabRepository::class)
 */
class Compestab
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string",length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $folio;

    /**
     * @ORM\Column(type="date")
     */
    private $femitido;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fimpreso;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fentrega;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fcancel;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sincostp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $duplicado;


    /**
     * @ORM\Column(type="float")
     */
    private $importe;

    /**
     * @ORM\ManyToOne(targetEntity=NombEstComp::class)
     */
    private $estadoComp;

    /**
     * @ORM\ManyToOne(targetEntity=CausaCancelacionComp::class)
     */
    private $cCancel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cSuspencion;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $finicioSusp;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $ffinSusp;

    /**
     * @ORM\Column(type="date")
     */
    private $fvencimiento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Instalaciones", inversedBy="comprobante")
     *  @ORM\JoinColumn(name="instalacion", referencedColumnName="id")
     */
    private $instalacion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Extension")
     */
    private $extensionID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firma;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firmacargo;



    /**
     * ComprobanteEstab constructor.
     */
    public function __construct()
    {
        $this->femitido = new \DateTime('today');
        $this->duplicado=false;
        $this->sincostp= false;
       // $this->extension="municipal";
        $this->importe= 0.0 ;
        $this->id = uniqid("comp:");
        $this->fvencimiento= new \DateTime($this->femitido->format('Y-m-d'));
        $this->fvencimiento->add(new \DateInterval('P3Y')) ;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id){
        $this->id= $id;
    }


    public function getFolio(): string
    {
        return $this->folio === null ? "":$this->folio ;

    }

    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;

        return $this;
    }

    public function getFemitido(): ?\DateTime
    {
        return $this->femitido;
    }

    public function setFemitido(?\DateTime $femitido): self
    {
        $this->femitido = $femitido;
        $this->fvencimiento= new \DateTime($this->femitido->format('Y-m-d'));
        $this->fvencimiento->add(new \DateInterval('P3Y'));
        return $this;
    }

    /**
     *
     */
    function __toString()
    {
        return $this->getFolio()==="" ? $this->getInstalacion()->getNombre() : $this->getFolio();
    }

    public function getFimpreso(): ?\DateTime
    {
        return $this->fimpreso;
    }

    public function setFimpreso(?\DateTime $fimpreso): self
    {
        $this->fimpreso = $fimpreso;

        return $this;
    }

    public function getFentrega(): ?\DateTime
    {
        return $this->fentrega;
    }

    public function setFentrega(?\DateTime $fentrega): self
    {
        $this->fentrega = $fentrega;

        return $this;
    }

    public function getFcancel(): ?\DateTime
    {
        return $this->fcancel;
    }

    public function setFcancel(?\DateTime $fcancel): self
    {
        $this->fcancel = $fcancel;

        return $this;
    }

    public function getSincostp(): ?bool
    {
        return $this->sincostp;
    }

    public function setSincostp(bool $sincostp): self
    {
        $this->sincostp = $sincostp;

        return $this;
    }

    public function getDuplicado(): ?bool
    {
        return $this->duplicado;
    }

    public function setDuplicado(bool $duplicado): self
    {
        $this->duplicado = $duplicado;

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

    public function getEstadoComp(): ?NombEstComp
    {
        return $this->estadoComp;
    }

    public function setEstadoComp(?NombEstComp $estadoComp): self
    {
        $this->estadoComp = $estadoComp;

        return $this;
    }

    public function getCCancel(): ?CausaCancelacionComp
    {
        return $this->cCancel;
    }

    public function setCCancel(?CausaCancelacionComp $cCancel): self
    {
        $this->cCancel = $cCancel;

        return $this;
    }

    public function getCSuspencion(): ?string
    {
        return $this->cSuspencion;
    }

    public function setCSuspencion(?string $cSuspencion): self
    {
        $this->cSuspencion = $cSuspencion;

        return $this;
    }

    public function getFinicioSusp(): ?\DateTimeInterface
    {
        return $this->finicioSusp;
    }

    public function setFinicioSusp(?\DateTimeInterface $finicioSusp): self
    {
        $this->finicioSusp = $finicioSusp;

        return $this;
    }

    public function getFfinSusp(): ?\DateTimeInterface
    {
        return $this->ffinSusp;
    }

    public function setFfinSusp(?\DateTimeInterface $ffinSusp): self
    {
        $this->ffinSusp = $ffinSusp;

        return $this;
    }

    public function getFvencimiento(): ?\DateTime
    {
        return $this->fvencimiento;
    }

    public function setFvencimiento(\DateTime $fvencimiento): self
    {
        $this->fvencimiento = $fvencimiento;

        return $this;
    }

    public function getInstalacion(): ?Instalaciones
    {
        return $this->instalacion;
    }

    public function setInstalacion(?Instalaciones $instalacion): self
    {
        $this->instalacion = $instalacion;

        return $this;
    }

    public function getExtensionID(): ?Extension
    {
        return $this->extensionID;
    }

    public function setExtensionID(?Extension $extensionID): self
    {
        $this->extensionID = $extensionID;

        return $this;
    }

    public function getFirma(): ?string
    {
        return $this->firma;
    }

    public function setFirma(?string $firma): self
    {
        $this->firma = $firma;

        return $this;
    }

    public function getFirmacargo(): ?string
    {
        return $this->firmacargo;
    }

    public function setFirmacargo(?string $firmacargo): self
    {
        $this->firmacargo = $firmacargo;

        return $this;
    }



}
