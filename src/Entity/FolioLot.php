<?php

namespace App\Entity;

use App\Repository\FolioLotRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FolioLotRepository::class)
 */
class FolioLot
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $valor;

    /**
     * FolioLot constructor.
     * @param $valor
     */
    public function __construct($valor)
    {
        $this->id = uniqid('foliolot:');
        $this->valor = $valor;
    }

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
}
