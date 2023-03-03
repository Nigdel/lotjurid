<?php

namespace App\Entity;

use App\Repository\TarifaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TarifaRepository::class)
 */
class Tarifa
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
    private $concepto;

    /**
     * @ORM\Column(type="float")
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity=Extension::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $extension;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConcepto(): ?string
    {
        return $this->concepto;
    }

    public function setConcepto(string $concepto): self
    {
        $this->concepto = $concepto;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getExtension(): ?Extension
    {
        return $this->extension;
    }

    public function setExtension(?Extension $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString():?string
    {
        return $this->getValor() ? $this->getValor(): ' ';
    }

}
