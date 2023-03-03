<?php

namespace App\Entity;

use App\Repository\InstalacionesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InstalacionesRepository::class)
 */
class Instalaciones
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Lotjuridicas::class, inversedBy="instalaciones")
     * @ORM\JoinColumn(referencedColumnName="NuLicencia", nullable=false)
     */
    private $lot;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direccion;

    /**
     * @ORM\ManyToOne(targetEntity=Municipios::class, inversedBy="instalaciones")
     * @ORM\JoinColumn(referencedColumnName="ID",nullable=false)
     */
    private $municipio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $aseguramiento;

    /**
     * @ORM\OneToMany(targetEntity=Compestab::class, mappedBy="instalacion", orphanRemoval=true)
     * @ORM\OrderBy({"femitido" = "ASC"})
     */
    private $comprobante;

    /**
     * @ORM\ManyToOne(targetEntity=TipoServAuxCon::class, inversedBy="instalaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $servAuxCon;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $servicios1 = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $servvisual;

//    /**
//     * @ORM\Column(type="json", nullable=true)
//     */
//    private $modificadorServicio = [];

    public function __construct()
    {
        $this->comprobante = new ArrayCollection();
        $this->id = uniqid();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getMunicipio(): ?Municipios
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipios $municipio): self
    {
        $this->municipio = $municipio;

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

    /**
     * @return Collection|Compestab[]
     */
    public function getComprobante(): Collection
    {
        return $this->comprobante;
    }

    public function addComprobante(Compestab $comprobante): self
    {
        if (!$this->comprobante->contains($comprobante)) {
            $this->comprobante[] = $comprobante;
            $comprobante->setInstalacion($this);
        }

        return $this;
    }

    public function removeComprobante(Compestab $comprobante): self
    {
        if ($this->comprobante->contains($comprobante)) {
            $this->comprobante->removeElement($comprobante);
            // set the owning side to null (unless already changed)
            if ($comprobante->getInstalacion() === $this) {
                $comprobante->setInstalacion(null);
            }
        }

        return $this;
    }

    public function getServAuxCon(): ?TipoServAuxCon
    {
        return $this->servAuxCon;
    }

    public function setServAuxCon(?TipoServAuxCon $servAuxCon): self
    {
        $this->servAuxCon = $servAuxCon;

        return $this;
    }


    /**
     * @return string
     */
    public function __toString()
    {
       return $this->getNombre().'. '.$this->getDireccion();
    }

    public function compActual(): ?Compestab
    {
        $date_compare= function (Compestab $a, Compestab $b){
            $t1 = strtotime($a->getFemitido()->format('Y-m-d'));
            $t2 = strtotime($b->getFemitido()->format('Y-m-d'));
            return $t1 - $t2;
        };
        $comps = $this->getComprobante()->toArray();
        if($comps != null){
            usort($comps, $date_compare);
            return $comps[count($comps)-1];
        }
        return null;
    }

    public function getServicios1(): ?array
    {
        return $this->servicios1;
    }

    public function setServicios1(array $servicio): self
    {
        $this->servicios1 = $servicio;

        return $this;
    }

    public function getServvisual(): ?string
    {
        return $this->servvisual;
    }

    public function setServvisual(?string $servvisual): self
    {
        $this->servvisual = $servvisual;

        return $this;
    }

}
