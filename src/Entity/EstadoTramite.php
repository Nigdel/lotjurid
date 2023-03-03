<?php

namespace App\Entity;

use App\Repository\EstadoTramiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EstadoTramiteRepository::class)
 */
class EstadoTramite
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
    private $estado;

    /**
     * @ORM\OneToMany(targetEntity=Tramite::class, mappedBy="estado")
     */
    private $tramites;

    public function __construct()
    {
        $this->tramites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

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
            $tramite->setEstado($this);
        }

        return $this;
    }

    public function removeTramite(Tramite $tramite): self
    {
        if ($this->tramites->contains($tramite)) {
            $this->tramites->removeElement($tramite);
            // set the owning side to null (unless already changed)
            if ($tramite->getEstado() === $this) {
                $tramite->setEstado(null);
            }
        }

        return $this;
    }
}
