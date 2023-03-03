<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Personasjuridicas
 *
 * @ORM\Table(name="personasjuridicas")
 * @ORM\Entity
 */
class Personasjuridicas
{
    /**
     * @var string $identidad
     * @ORM\Id
     * @ORM\Column(name="IdEntidad", type="string",length=50)
     *
     */
    private $id;

    /**
     * Personasjuridicas constructor.
//     * @param Municipios $mcpio
     */
    public function __construct(/*Municipios $mcpio*/)
    {
//        $this->setIdmunicipio($mcpio);
        $this->lot = new ArrayCollection();
        $this->tramites = new ArrayCollection();
    }


    /**
     * @param string $id
     *  @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var string $codreeup
     *
     * @ORM\Column(name="CodReeup", type="string", length=50, nullable=true)
     */
    private $codreeup;

    /**
     * @var string $nomentidad
     *
     * @ORM\Column(name="NomEntidad", type="string", length=100, nullable=false)
     */
    private $nomentidad;

    /**
     * @var string $telefono
     *
     * @ORM\Column(name="Telefono", type="string", length=20, nullable=true)
     */
    private $telefono;

    /**
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     * @return Personasjuridicas
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Personasjuridicas
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @var string $email
     *
     * @ORM\Column(name="Email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @var Organismos $idorga
     * @ORM\ManyToOne(targetEntity="App\Entity\Organismos")
     * @ORM\JoinColumn(name="idorga", referencedColumnName="Cod")
     */
    private $idorga;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="Direccion", type="string", length=200, nullable=true)
     */
    private $direccion;

    /**
     * @var Municipios $idmunicipio
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Municipios")
     * @ORM\JoinColumn(name="idmunicipio", referencedColumnName="ID")
     */
    private $idmunicipio;

    /**
     * @var string $actividad
     *
     * @ORM\Column(name="Actividad", type="string", length=2, nullable=true)
     */
    private $actividad;

    /**
     * @var string $rama
     *
     * @ORM\Column(name="Rama", type="string", length=2, nullable=true)
     */
    private $rama;

    /**
     * @var string $subrama
     *
     * @ORM\Column(name="SubRama", type="string", length=2, nullable=true)
     */
    private $subrama;

    /**
     * @var string $nocontribuyente
     *
     * @ORM\Column(name="NoContribuyente", type="string", length=15, nullable=true)
     */
    private $nocontribuyente;

    /**
     * @var string $representante
     *
     * @ORM\Column(name="Representante", type="string", length=50, nullable=true)
     */
    private $representante;

    /**
     * @ORM\OneToMany(targetEntity=Lotjuridicas::class, mappedBy="identidad")
     */
    private $lot;

    /**
     * @ORM\OneToMany(targetEntity=Tramite::class, mappedBy="pj")
     */
    private $tramites;

    /**
     * @ORM\ManyToOne(targetEntity=TipoEmpresa::class)
     * @ORM\JoinColumn(name="tipoEmpresa", referencedColumnName="id")
     */
    private $tipoEmpresa;

    /**
     * @return mixed
     */
    public function getRepresentante()
    {
        return $this->representante;
    }

    /**
     * @param mixed $representante
     * @return Personasjuridicas
     */
    public function setRepresentante($representante)
    {
        $this->representante = $representante;
        return $this;
    }


    /**
     * Get identidad
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codreeup
     *
     * @param string $codreeup
     * @return Personasjuridicas
     */
    public function setCodreeup($codreeup)
    {
        $this->codreeup = $codreeup;
    
        return $this;
    }

    /**
     * Get codreeup
     *
     * @return string 
     */
    public function getCodreeup()
    {
        return $this->codreeup;
    }

    /**
     * Set nomentidad
     *
     * @param string $nomentidad
     * @return Personasjuridicas
     */
    public function setNomentidad($nomentidad)
    {
        $this->nomentidad = $nomentidad;
    
        return $this;
    }

    /**
     * Get nomentidad
     *
     * @return string 
     */
    public function getNomentidad()
    {
        return $this->nomentidad;
    }

    /**
     * Set idorga
     *
     * @param Organismos $idorga
     * @return Personasjuridicas
     */
    public function setIdorga($idorga)
    {
        $this->idorga = $idorga;
    
        return $this;
    }

    /**
     * Get idorga
     *
     * @return Organismos
     */
    public function getIdorga()
    {

        return $this->idorga;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Personasjuridicas
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    
        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set idmunicipio
     *
     * @param Municipios $idmunicipio
     * @return Personasjuridicas
     */
    public function setIdmunicipio($idmunicipio)
    {
        $this->idmunicipio = $idmunicipio;
    
        return $this;
    }

    /**
     * Get idmunicipio
     *
     * @return Municipios
     */
    public function getIdmunicipio()
    {
        return $this->idmunicipio;
    }
        /**
     * Set actividad
     *
     * @param string $actividad
     * @return Personasjuridicas
     */
    public function setActividad($actividad)
    {
        $this->actividad = $actividad;
    
        return $this;
    }

    /**
     * Get actividad
     *
     * @return string 
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set rama
     *
     * @param string $rama
     * @return Personasjuridicas
     */
    public function setRama($rama)
    {
        $this->rama = $rama;
    
        return $this;
    }

    /**
     * Get rama
     *
     * @return string 
     */
    public function getRama()
    {
        return $this->rama;
    }

    /**
     * Set subrama
     *
     * @param string $subrama
     * @return Personasjuridicas
     */
    public function setSubrama($subrama)
    {
        $this->subrama = $subrama;
    
        return $this;
    }

    /**
     * Get subrama
     *
     * @return string 
     */
    public function getSubrama()
    {
        return $this->subrama;
    }

    /**
     * Set nocontribuyente
     *
     * @param string $nocontribuyente
     * @return Personasjuridicas
     */
    public function setNocontribuyente($nocontribuyente)
    {
        $this->nocontribuyente = $nocontribuyente;
    
        return $this;
    }

    /**
     * Get nocontribuyente
     *
     * @return string 
     */
    public function getNocontribuyente()
    {
        return $this->nocontribuyente;
    }

    public function __toString()
    {
        $org= $this->getIdorga();
        if($org){
            $orgdesc= $this->getIdorga()->getDesc() ? $this->getIdorga()->getDesc():'';
            return $orgdesc.'. '.$this->getNomentidad();
        }
        return 'Sin Organismo. '.$this->getNomentidad();
    }

    /**
     * @return Collection|Lotjuridicas[]
     */
    public function getLot(): Collection
    {
        return $this->lot;
    }

    public function addLot(Lotjuridicas $lot): self
    {
        if (!$this->lot->contains($lot)) {
            $this->lot[] = $lot;
            $lot->setIdentidad($this);
        }

        return $this;
    }

    public function removeLot(Lotjuridicas $lot): self
    {
        if ($this->lot->contains($lot)) {
            $this->lot->removeElement($lot);
            // set the owning side to null (unless already changed)
            if ($lot->getIdentidad() === $this) {
                $lot->setIdentidad(null);
            }
        }

        return $this;
    }

    /**
     * @return Lotjuridicas
     */
    public function getCurrentLot(){
        return $this->lot->last();
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
            $tramite->setPj($this);
        }

        return $this;
    }

    public function removeTramite(Tramite $tramite): self
    {
        if ($this->tramites->contains($tramite)) {
            $this->tramites->removeElement($tramite);
            // set the owning side to null (unless already changed)
            if ($tramite->getPj() === $this) {
                $tramite->setPj(null);
            }
        }

        return $this;
    }

    public function getTipoEmpresa(): ?TipoEmpresa
    {
        return $this->tipoEmpresa;
    }

    public function setTipoEmpresa(?TipoEmpresa $tipoEmpresa): self
    {
        $this->tipoEmpresa = $tipoEmpresa;

        return $this;
    }
}