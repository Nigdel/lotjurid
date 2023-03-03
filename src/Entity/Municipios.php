<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Municipios
 *
 * @ORM\Table(name="municipios")
 * @ORM\Entity
 */
class Municipios
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var string $sGuid
     *
     * @ORM\Column(name="s_GUID", type="string", length=50, nullable=true)
     */
    private $sGuid;

    /**
     * @var Provincias $provinciaid
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Provincias")
     * @ORM\JoinColumn(name="provinciaid", referencedColumnName="ID")
     */
    private $provinciaid;

    /**
     * @var integer $codigo
     *
     * @ORM\Column(name="Codigo", type="integer", nullable=true)
     */
    private $codigo;

    /**
     * @var string $municipio
     *
     * @ORM\Column(name="Municipio", type="string", length=255, nullable=true)
     */
    private $municipio;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="municipio")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=OficinaMcpal::class, mappedBy="municipio")
     */
    private $oficinaMcpals;

    /**
     * @ORM\OneToMany(targetEntity=Instalaciones::class, mappedBy="municipio")
     */
    private $instalaciones;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->oficinaMcpals = new ArrayCollection();
        $this->instalaciones = new ArrayCollection();
    }



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sGuid
     *
     * @param string $sGuid
     * @return Municipios
     */
    public function setSGuid($sGuid)
    {
        $this->sGuid = $sGuid;
    
        return $this;
    }

    /**
     * Get sGuid
     *
     * @return string 
     */
    public function getSGuid()
    {
        return $this->sGuid;
    }

    /**
     * Set provinciaid
     *
     * @param Provincias $provinciaid
     * @return Municipios
     */
    public function setProvinciaid($provinciaid)
    {
        $this->provinciaid = $provinciaid;
    
        return $this;
    }

    /**
     * Get provinciaid
     *
     * @return Provincias
     */
    public function getProvinciaid()
    {
        return $this->provinciaid;
    }

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return Municipios
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set municipio
     *
     * @param string $municipio
     * @return Municipios
     */
    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
    
        return $this;
    }

    /**
     * Get municipio
     *
     * @return string 
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    public function __toString(){
        return ($this->municipio)."($this->provinciaid)" ;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setMunicipio($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getMunicipio() === $this) {
                $user->setMunicipio(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OficinaMcpal[]
     */
    public function getOficinaMcpals(): Collection
    {
        return $this->oficinaMcpals;
    }

    public function addOficinaMcpal(OficinaMcpal $oficinaMcpal): self
    {
        if (!$this->oficinaMcpals->contains($oficinaMcpal)) {
            $this->oficinaMcpals[] = $oficinaMcpal;
            $oficinaMcpal->setMunicipio($this);
        }

        return $this;
    }

    public function removeOficinaMcpal(OficinaMcpal $oficinaMcpal): self
    {
        if ($this->oficinaMcpals->contains($oficinaMcpal)) {
            $this->oficinaMcpals->removeElement($oficinaMcpal);
            // set the owning side to null (unless already changed)
            if ($oficinaMcpal->getMunicipio() === $this) {
                $oficinaMcpal->setMunicipio(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Instalaciones[]
     */
    public function getInstalaciones(): Collection
    {
        return $this->instalaciones;
    }

    public function addInstalacione(Instalaciones $instalacione): self
    {
        if (!$this->instalaciones->contains($instalacione)) {
            $this->instalaciones[] = $instalacione;
            $instalacione->setMunicipio($this);
        }

        return $this;
    }

    public function removeInstalacione(Instalaciones $instalacione): self
    {
        if ($this->instalaciones->contains($instalacione)) {
            $this->instalaciones->removeElement($instalacione);
            // set the owning side to null (unless already changed)
            if ($instalacione->getMunicipio() === $this) {
                $instalacione->setMunicipio(null);
            }
        }

        return $this;
    }
}