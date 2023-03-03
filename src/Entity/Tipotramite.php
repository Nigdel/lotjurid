<?php

namespace App\Entity;

use App\Repository\TipotramiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TipotramiteRepository::class)
 */
class Tipotramite
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
    private $nombretipotramite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $remunerado;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $importe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombretipotramite(): ?string
    {
        return $this->nombretipotramite;
    }

    public function setNombretipotramite(string $nombretipotramite): self
    {
        $this->nombretipotramite = $nombretipotramite;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->nombretipotramite;
    }

    public function getRemunerado(): ?bool
    {
        return $this->remunerado;
    }

    public function setRemunerado(bool $remunerado): self
    {
        $this->remunerado = $remunerado;

        return $this;
    }

    public function getImporte(): ?float
    {
        return $this->importe;
    }

    public function setImporte(?float $importe): self
    {
        $this->importe = $importe;

        return $this;
    }
}
