<?php

namespace App\Entity;

use App\Repository\TramiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TramiteRepository::class)
 */
class Tramite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tramites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Tipotramite::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipotramite;

    /**
     * @ORM\ManyToOne(targetEntity=Lotjuridicas::class, inversedBy="tramites")
     * @ORM\JoinColumn(nullable=true,referencedColumnName="NuLicencia")
     */
    private $lot;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Iporigen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\ManyToOne(targetEntity=EstadoTramite::class, inversedBy="tramites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="aprobaciondetramites")
     */
    private $aprueba;

    /**
     * @ORM\ManyToOne(targetEntity=Personasjuridicas::class, inversedBy="tramites")
     * @ORM\JoinColumn(nullable=true,referencedColumnName="IdEntidad")
     */
    private $pj;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getTipotramite(): ?Tipotramite
    {
        return $this->tipotramite;
    }

    public function setTipotramite(?Tipotramite $tipotramite): self
    {
        $this->tipotramite = $tipotramite;

        return $this;
    }

    public function getLot(): ?Lotjuridicas
    {
        return $this->lot;
    }

    public function setLot(?Lotjuridicas $lot): self
    {
        $this->lot = $lot;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getIporigen(): ?string
    {
        return $this->Iporigen;
    }

    public function setIporigen(?string $Iporigen): self
    {
        $this->Iporigen = $Iporigen;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getEstado(): ?EstadoTramite
    {
        return $this->estado;
    }

    public function setEstado(?EstadoTramite $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getAprueba(): ?User
    {
        return $this->aprueba;
    }

    public function setAprueba(?User $aprueba): self
    {
        $this->aprueba = $aprueba;

        return $this;
    }

    public function getPj(): ?Personasjuridicas
    {
        return $this->pj;
    }

    public function setPj(?Personasjuridicas $pj): self
    {
        $this->pj = $pj;

        return $this;
    }
}
