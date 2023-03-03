<?php

namespace App\Entity;

use App\Repository\TipoEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TipoEmpresaRepository::class)
 */
class TipoEmpresa
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ucfirst($this->getTipo());
    }


}
