<?php

namespace App\Entity;

use App\Repository\DireccionProvincialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DireccionProvincialRepository::class)
 */
class DireccionProvincial
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
    private $direccion;

    /**
     * @ORM\ManyToOne(targetEntity=Provincias::class, inversedBy="direccionProvincials")
     * @ORM\JoinColumn(nullable=false,referencedColumnName="ID")
     */
    private $provincia;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $telefonos;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $directorProvincial;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $subdirLot;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $subdirGral;



    /**
     * @ORM\OneToMany(targetEntity=OficinaMcpal::class, mappedBy="direccionProvincial")
     */
    private $oficinasMunicipales;

    /**
     * @ORM\ManyToOne(targetEntity=DireccionNacional::class, inversedBy="direccionesProvinciales")
     * @ORM\JoinColumn(nullable=false)
     */
    private $direccionNacional;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="direccionProvincial")
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

        $this->oficinasMunicipales = new ArrayCollection();
        $this->funcionarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getProvincia(): ?Provincias
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincias $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getDirectorProvincial(): ?User
    {
        return $this->directorProvincial;
    }

    public function setDirectorProvincial(User $directorProvincial): self
    {
        $this->directorProvincial = $directorProvincial;

        return $this;
    }

    public function getSubdirLot(): ?User
    {
        return $this->subdirLot;
    }

    public function setSubdirLot(User $subdirLot): self
    {
        $this->subdirLot = $subdirLot;

        return $this;
    }

    public function getSubdirGral(): ?User
    {
        return $this->subdirGral;
    }

    public function setSubdirGral(?User $subdirGral): self
    {
        $this->subdirGral = $subdirGral;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getProvincia()->__toString();
    }

    /**
     * @return Collection|OficinaMcpal[]
     */
    public function getOficinasMunicipales(): Collection
    {
        return $this->oficinasMunicipales;
    }

    public function addOficinasMunicipale(OficinaMcpal $oficinasMunicipale): self
    {
        if (!$this->oficinasMunicipales->contains($oficinasMunicipale)) {
            $this->oficinasMunicipales[] = $oficinasMunicipale;
            $oficinasMunicipale->setDireccionProvincial($this);
        }

        return $this;
    }

    public function removeOficinasMunicipale(OficinaMcpal $oficinasMunicipale): self
    {
        if ($this->oficinasMunicipales->contains($oficinasMunicipale)) {
            $this->oficinasMunicipales->removeElement($oficinasMunicipale);
            // set the owning side to null (unless already changed)
            if ($oficinasMunicipale->getDireccionProvincial() === $this) {
                $oficinasMunicipale->setDireccionProvincial(null);
            }
        }

        return $this;
    }

    public function getDireccionNacional(): ?DireccionNacional
    {
        return $this->direccionNacional;
    }

    public function setDireccionNacional(?DireccionNacional $direccionNacional): self
    {
        $this->direccionNacional = $direccionNacional;

        return $this;
    }

    public function getTelefonos(): ?string
    {
        return $this->telefonos;
    }

    public function setTelefonos(?string $telefonos): self
    {
        $this->telefonos = $telefonos;

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
            $funcionario->setDireccionProvincial($this);
        }

        return $this;
    }

    public function removeFuncionario(User $funcionario): self
    {
        if ($this->funcionarios->contains($funcionario)) {
            $this->funcionarios->removeElement($funcionario);
            // set the owning side to null (unless already changed)
            if ($funcionario->getDireccionProvincial() === $this) {
                $funcionario->setDireccionProvincial(null);
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
