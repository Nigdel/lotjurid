<?php

namespace App\Entity;

use App\Repository\ComprobanteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity(repositoryClass=ComprobanteRepository::class)
 */
class Comprobante
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string",length=255)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MediosTrans::class, inversedBy="comprobantes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medio;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $folio;

    /**
     *  @ORM\ManyToOne(targetEntity="App\Entity\Lotjuridicas", inversedBy="comprobantes",fetch="EAGER")
     *  @ORM\JoinColumn(name="lot", referencedColumnName="NuLicencia")
     */
    private $lot;

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
     *  @ORM\ManyToOne(targetEntity="App\Entity\Extension")
     */
    private $extension;

    /**
     * @ORM\Column(type="float")
     */
    private $importe;

    /**
     * @var NombEstComp
     * @ORM\ManyToOne(targetEntity=NombEstComp::class)
     */
    private $estadoComp;

    /**
     * @ORM\ManyToOne(targetEntity=CausaCancelacionComp::class)
     */
    private $cCancel;

    /**
     * @ORM\ManyToOne(targetEntity=CausaSuspensionComp::class)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firma;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $Firmacargo;


    /**
     * Comprobante constructor.
     */
    public function __construct()
    {
        $this->medio= null;
        $this->lot = null;
        $this->femitido = null;
        $this->duplicado=false;
        $this->sincostp= false;
        $this->importe= 0.0 ;
        $this->id = uniqid("comp:");
        $this->fvencimiento= null  /*new \DateTime($this->femitido->format('Y-m-d'))*/;
//        $this->fvencimiento->add(new \DateInterval('P3Y')) ;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id){
        $this->id= $id;
    }

    public function getMedio(): ?MediosTrans
    {
        return $this->medio;
    }

    public function setMedio(?MediosTrans $medio): self
    {
        $this->medio = $medio;

        return $this;
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

    public function getLot(): ?Lotjuridicas
    {
        return $this->lot;
    }

    public function setLot(Lotjuridicas $lot): self
    {
        $this->lot = $lot;

        return $this;
    }

    public function getFemitido(): ?\DateTime
    {
        return $this->femitido;
    }

    /**
     * @param \DateTime|null $femitido
     * @return Comprobante
     * @throws \Exception
     */
    public function setFemitido(?\DateTime $femitido): self
    {
        $this->femitido = $femitido;
//        $this->fvencimiento= new \DateTime($this->femitido->format('Y-m-d'));
//        if($this->duplicado===false)
//        $this->fvencimiento->add(new \DateInterval('P3Y'));
        return $this;
    }

    /**
     *
     */
    function __toString()
    {
        //return $this->getMedio()->getNombre()."\t".$this->getLot()->getId()."\t".$this->getFolio()->getValor()."\t".$this->getEstadoComp()->getNombreEstado()."\t".$this->getFemitido()->format("d/m/Y");

        return $this->getFolio()==="" ? '' : $this->getFolio();
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

    public function getExtension(): ?Extension
    {
        return $this->extension;
    }

    public function setExtension(Extension $extension): self
    {
        $this->extension = $extension;

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

    public function getCSuspencion(): ?CausaSuspensionComp
    {
        return $this->cSuspencion;
    }

    public function setCSuspencion(?CausaSuspensionComp $cSuspencion): self
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
        dump(new DateTime());
        return $this;
    }
    public function getServicio(): string
    {
        $medServic = $this->getLot()->getServicioamparado();
        if($this->getMedio()){
            $medServic= $this->getMedio()->getServicio();
            if($this->getMedio()->getAseguramiento())
                $medServic.='. Aseguramiento';
        }
        return $medServic.'.';
    }
    public function editable(): bool{
        if($this->getEstadoComp()->getId()==2)
            return true;
        return false;
    }
    public function editablebyme(User $user):bool {
        if($this->editable() && ($this->getMedio()->getBasificacionObj()->getIdmun()->getId() == $user->getMunicipio()->getId()) )
            return true;
        return false;
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
        return $this->Firmacargo;
    }

    public function setFirmacargo(string $Firmacargo): self
    {
        $this->Firmacargo = $Firmacargo;

        return $this;
    }
    
}
