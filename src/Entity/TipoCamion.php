<?php

namespace App\Entity;

use App\Repository\TipoCamionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TipoCamionRepository::class)
 */
class TipoCamion
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
    private $NombreTipoCamion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreTipoCamion(): ?string
    {
        return $this->NombreTipoCamion;
    }

    public function setNombreTipoCamion(?string $NombreTipoCamion): self
    {
        $this->NombreTipoCamion = $NombreTipoCamion;

        return $this;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getNombreTipoCamion();
    }
}
