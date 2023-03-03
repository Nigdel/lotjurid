<?php

namespace App\Entity;

use App\Repository\UmcapRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UmcapRepository::class)
 */
class Umcap
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
    private $valor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValor() ? $this->getValor() : '';
    }
}
