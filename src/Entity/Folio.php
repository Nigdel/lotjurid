<?php

namespace App\Entity;

use App\Repository\FolioRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FolioRepository::class)
 */
class Folio
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $valor;

    public function __toString()
    {
        return $this->getValor();
    }

    /**
     * Folio constructor.
     * @param $valor
     */
    public function __construct( $valor)
    {
        $this->id = uniqid('Folio:');
        $this->valor = $valor;
    }

    public function getId(): ?string
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
}
