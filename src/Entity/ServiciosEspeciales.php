<?php

namespace App\Entity;

use App\Repository\ServiciosEspecialesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiciosEspecialesRepository::class)
 */
class ServiciosEspeciales
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
    private $servicioEsp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServicioEsp(): ?string
    {
        return $this->servicioEsp;
    }

    public function setServicioEsp(string $servicioEsp): self
    {
        $this->servicioEsp = $servicioEsp;

        return $this;
    }
}
