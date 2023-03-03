<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Municipios::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="ID")
     */
    private $municipio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombreApellidos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $carnetFuncionario;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaContratacion;

    /**
     * @ORM\Column(type="integer")
     */
    private $nivelAcceso;

    /**
     * @ORM\ManyToOne(targetEntity=OficinaMcpal::class, inversedBy="funcionarios")
     */
    private $oficinaMcpal;

    /**
     * @ORM\ManyToOne(targetEntity=DireccionProvincial::class, inversedBy="funcionarios")
     */
    private $direccionProvincial;

    /**
     * @ORM\OneToMany(targetEntity=Tramite::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $tramites;

    /**
     * @ORM\ManyToOne(targetEntity=CargosUet::class)
     */
    private $cargo;

    /**
     * @ORM\OneToMany(targetEntity=Tramite::class, mappedBy="aprueba")
     */
    private $aprobaciondetramites;

    /**
     * @ORM\OneToMany(targetEntity=Mensaje::class, mappedBy="envia", orphanRemoval=true)
     */
    private $mensajesenviados;

    /**
     * @ORM\OneToMany(targetEntity=Mensaje::class, mappedBy="recibe", orphanRemoval=true)
     * @ORM\OrderBy({"fechaenvio" = "DESC"})
     */
    private $mismensajes;


    public function __construct()
    {
        $this->nivelAcceso=0;
        $this->tramites = new ArrayCollection();
        $this->aprobaciondetramites = new ArrayCollection();
        $this->mensajesenviados = new ArrayCollection();
        $this->mismensajes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function __toString()
    {
        return $this->getEmail();
    }

    public function getNombreApellidos(): ?string
    {
        return $this->nombreApellidos;
    }

    public function setNombreApellidos(?string $nombreApellidos): self
    {
        $this->nombreApellidos = $nombreApellidos;

        return $this;
    }

    public function getCarnetFuncionario(): ?string
    {
        return $this->carnetFuncionario;
    }

    public function setCarnetFuncionario(?string $carnetFuncionario): self
    {
        $this->carnetFuncionario = $carnetFuncionario;

        return $this;
    }

    public function getFechaContratacion(): ?\DateTimeInterface
    {
        return $this->fechaContratacion;
    }

    public function setFechaContratacion(\DateTimeInterface $fechaContratacion): self
    {
        $this->fechaContratacion = $fechaContratacion;

        return $this;
    }

    public function getNivelAcceso(): ?int
    {
        return $this->nivelAcceso;
    }

    public function setNivelAcceso(int $nivelAcceso): self
    {
        $this->nivelAcceso = $nivelAcceso;

        return $this;
    }

    /**
     * @return bool
     */
    public function perfilTerminado(){
        if($this->nombreApellidos!=null&&$this->nombreApellidos!=""
            &&$this->nivelAcceso&&$this->fechaContratacion!=null
        )
        return true;
        return false;
    }

    public function getOficinaMcpal(): ?OficinaMcpal
    {
        return $this->oficinaMcpal;
    }

    public function setOficinaMcpal(?OficinaMcpal $oficinaMcpal): self
    {
        $this->oficinaMcpal = $oficinaMcpal;

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
     * @return Collection|Tramite[]
     */
    public function getTramites(): Collection
    {
        return $this->tramites;
    }

    public function addTramite(Tramite $tramite): self
    {
        if (!$this->tramites->contains($tramite)) {
            $this->tramites[] = $tramite;
            $tramite->setUsuario($this);
        }

        return $this;
    }

    public function removeTramite(Tramite $tramite): self
    {
        if ($this->tramites->contains($tramite)) {
            $this->tramites->removeElement($tramite);
            // set the owning side to null (unless already changed)
            if ($tramite->getUsuario() === $this) {
                $tramite->setUsuario(null);
            }
        }

        return $this;
    }

    public function getCargo(): ?CargosUet
    {
        return $this->cargo;
    }

    public function setCargo(?CargosUet $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * @return Collection|Tramite[]
     */
    public function getAprobaciondetramites(): Collection
    {
        return $this->aprobaciondetramites;
    }

    public function addAprobaciondetramite(Tramite $aprobaciondetramite): self
    {
        if (!$this->aprobaciondetramites->contains($aprobaciondetramite)) {
            $this->aprobaciondetramites[] = $aprobaciondetramite;
            $aprobaciondetramite->setAprueba($this);
        }

        return $this;
    }

    public function removeAprobaciondetramite(Tramite $aprobaciondetramite): self
    {
        if ($this->aprobaciondetramites->contains($aprobaciondetramite)) {
            $this->aprobaciondetramites->removeElement($aprobaciondetramite);
            // set the owning side to null (unless already changed)
            if ($aprobaciondetramite->getAprueba() === $this) {
                $aprobaciondetramite->setAprueba(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Mensaje[]
     */
    public function getMensajesenviados(): Collection
    {
        return $this->mensajesenviados;
    }

    public function addMensajesenviado(Mensaje $mensajesenviado): self
    {
        if (!$this->mensajesenviados->contains($mensajesenviado)) {
            $this->mensajesenviados[] = $mensajesenviado;
            $mensajesenviado->setEnvia($this);
        }

        return $this;
    }

    public function removeMensajesenviado(Mensaje $mensajesenviado): self
    {
        if ($this->mensajesenviados->contains($mensajesenviado)) {
            $this->mensajesenviados->removeElement($mensajesenviado);
            // set the owning side to null (unless already changed)
            if ($mensajesenviado->getEnvia() === $this) {
                $mensajesenviado->setEnvia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Mensaje[]
     */
    public function getMismensajes(): Collection
    {
        return $this->mismensajes;
    }

    public function addMismensaje(Mensaje $mismensaje): self
    {
        if (!$this->mismensajes->contains($mismensaje)) {
            $this->mismensajes[] = $mismensaje;
            $mismensaje->setRecibe($this);
        }

        return $this;
    }

    public function removeMismensaje(Mensaje $mismensaje): self
    {
        if ($this->mismensajes->contains($mismensaje)) {
            $this->mismensajes->removeElement($mismensaje);
            // set the owning side to null (unless already changed)
            if ($mismensaje->getRecibe() === $this) {
                $mismensaje->setRecibe(null);
            }
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getMensajesUnread(): int
    {
        $cont=0;
       foreach ($this->mismensajes as $msj){
           if($msj->getLeido()===false){
               $cont++;
           }
       }
       return $cont;
    }



}
