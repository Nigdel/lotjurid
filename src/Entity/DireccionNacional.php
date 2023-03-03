<?php

namespace App\Entity;

use App\Repository\DireccionNacionalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DireccionNacionalRepository::class)
 */
class DireccionNacional
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direccion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefonos;



    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $director;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $subdirLot;

    /**
     * @ORM\OneToMany(targetEntity=DireccionProvincial::class, mappedBy="direccionNacional")
     */
    private $direccionesProvinciales;

    public function __construct()
    {
        $this->direccionesProvinciales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTelefonos(): ?string
    {
        return $this->telefonos;
    }

    public function setTelefonos(?string $telefonos): self
    {
        $this->telefonos = $telefonos;

        return $this;
    }

    /**
     * @return Collection|User[]
     */

    public function getDirector(): ?User
    {
        return $this->director;
    }

    public function setDirector(User $director): self
    {
        $this->director = $director;

        return $this;
    }

    public function getSubdirLot(): ?User
    {
        return $this->subdirLot;
    }

    public function setSubdirLot(User $subdirLot): self
    {
        $this->subdirLot = $subdirLot;

        return $this;
    }

    /**
     * @return Collection|DireccionProvincial[]
     */
    public function getDireccionesProvinciales(): Collection
    {
        return $this->direccionesProvinciales;
    }

    public function addDireccionesProvinciale(DireccionProvincial $direccionesProvinciale): self
    {
        if (!$this->direccionesProvinciales->contains($direccionesProvinciale)) {
            $this->direccionesProvinciales[] = $direccionesProvinciale;
            $direccionesProvinciale->setDireccionNacional($this);
        }

        return $this;
    }

    public function removeDireccionesProvinciale(DireccionProvincial $direccionesProvinciale): self
    {
        if ($this->direccionesProvinciales->contains($direccionesProvinciale)) {
            $this->direccionesProvinciales->removeElement($direccionesProvinciale);
            // set the owning side to null (unless already changed)
            if ($direccionesProvinciale->getDireccionNacional() === $this) {
                $direccionesProvinciale->setDireccionNacional(null);
            }
        }

        return $this;
    }
}
