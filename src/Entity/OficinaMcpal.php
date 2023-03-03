<?php

namespace App\Entity;

use App\Repository\OficinaMcpalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OficinaMcpalRepository::class)
 */
class OficinaMcpal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $director;

    /**
     * @ORM\ManyToOne(targetEntity=Municipios::class, inversedBy="oficinaMcpals")
     * @ORM\JoinColumn(nullable=false,referencedColumnName="ID")
     */
    private $municipio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=DireccionProvincial::class, inversedBy="oficinasMunicipales")
     */
    private $direccionProvincial;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="oficinaMcpal")
     */
    private $funcionarios;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $firmalot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $firmacomp;

     public function __construct()
     {
         $this->funcionarios = new ArrayCollection();
     }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Ofic Mcpal ".$this->getMunicipio()->__toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDirector(): ?User
    {
        return $this->director;
    }

    public function setDirector(User $director): self
    {
        $this->director = $director;

        return $this;
    }


    public function getMunicipio(): ?Municipios
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipios $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?int
    {
        return $this->telefono;
    }

    public function setTelefono(?int $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDireccionProvincial(): ?DireccionProvincial
    {
        return $this->direccionProvincial;
    }

    public function setDireccionProvincial(?DireccionProvincial $direccionProvincial): self
    {
        $this->direccionProvincial = $direccionProvincial;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFuncionarios(): Collection
    {
        return $this->funcionarios;
    }

    public function addFuncionario(User $funcionario): self
    {
        if (!$this->funcionarios->contains($funcionario)) {
            $this->funcionarios[] = $funcionario;
            $funcionario->setOficinaMcpal($this);
        }

        return $this;
    }

    public function removeFuncionario(User $funcionario): self
    {
        if ($this->funcionarios->contains($funcionario)) {
            $this->funcionarios->removeElement($funcionario);
            // set the owning side to null (unless already changed)
            if ($funcionario->getOficinaMcpal() === $this) {
                $funcionario->setOficinaMcpal(null);
            }
        }

        return $this;
    }

    public function getFirmalot(): ?User
    {
        return $this->firmalot;
    }

    public function setFirmalot(?User $firmalot): self
    {
        $this->firmalot = $firmalot;

        return $this;
    }

    public function getFirmacomp(): ?User
    {
        return $this->firmacomp;
    }

    public function setFirmacomp(?User $firmacomp): self
    {
        $this->firmacomp = $firmacomp;

        return $this;
    }




}
