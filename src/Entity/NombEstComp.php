<?php

namespace App\Entity;

use App\Repository\NombEstCompRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NombEstCompRepository::class)
 */
class NombEstComp
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
    private $nombreEstado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreEstado(): ?string
    {
        return $this->nombreEstado;
    }

    public function setNombreEstado(string $nombreEstado): self
    {
        $this->nombreEstado = $nombreEstado;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->nombreEstado;
    }
}
