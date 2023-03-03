<?php

namespace App\Entity;

use App\Repository\CargosUetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CargosUetRepository::class)
 */
class CargosUet
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
    private $cargo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCargo(): ?string
    {
        return $this->cargo;
    }

    public function setCargo(string $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return $this->getCargo();
    }
}
