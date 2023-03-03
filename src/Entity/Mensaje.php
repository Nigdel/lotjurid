<?php

namespace App\Entity;

use App\Repository\MensajeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MensajeRepository::class)
 */
class Mensaje
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mensajesenviados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $envia;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mismensajes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recibe;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $asunto;

    /**
     * @ORM\Column(type="text")
     */
    private $texto;

//    /**
//     * @ORM\ManyToOne(targetEntity=EstadoMensaje::class)
//     * @ORM\JoinColumn(nullable=false)
//     */
//    private $estado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaenvio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $leido;

    /**
     * Mensaje constructor.
     */
    public function __construct()
    {
        $this->fechaenvio = new \DateTime();
        $this->leido = false;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnvia(): ?User
    {
        return $this->envia;
    }

    public function setEnvia(?User $envia): self
    {
        $this->envia = $envia;

        return $this;
    }

    public function getRecibe(): ?User
    {
        return $this->recibe;
    }

    public function setRecibe(?User $recibe): self
    {
        $this->recibe = $recibe;

        return $this;
    }

    public function getAsunto(): ?string
    {
        return $this->asunto;
    }

    public function setAsunto(?string $asunto): self
    {
        $this->asunto = $asunto;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;

        return $this;
    }

//    public function getEstado(): ?EstadoMensaje
//    {
//        return $this->estado;
//    }
//
//    public function setEstado(?EstadoMensaje $estado): self
//    {
//        $this->estado = $estado;
//
//        return $this;
//    }

    public function getFechaenvio(): ?\DateTimeInterface
    {
        return $this->fechaenvio;
    }

    public function setFechaenvio(\DateTimeInterface $fechaenvio): self
    {
        $this->fechaenvio = $fechaenvio;

        return $this;
    }

    public function getLeido(): ?bool
    {
        return $this->leido;
    }

    public function setLeido(bool $leido): self
    {
        $this->leido = $leido;

        return $this;
    }
}
