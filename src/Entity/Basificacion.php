<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Basificacion
 *
 * @ORM\Table(name="basificacion")
 * @ORM\Entity
 */
class Basificacion
{
    /**
     * @var integer $idlbasiam
     *
     * @ORM\Column(name="IdLBasiAM", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idlbasiam;

    /**
     * @var Lotjuridicas $idlicencia
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Lotjuridicas", inversedBy="basificaciones",fetch="EAGER")
     * @ORM\JoinColumn(name="idlicencia", referencedColumnName="NuLicencia")
     */
    private $idlicencia;

    /**
     * @var string $nombrelb
     *
     * @ORM\Column(name="NombreLB", type="string", length=200, nullable=false)
     */
    private $nombrelb;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="Direccion", type="string", length=200, nullable=false)
     */
    private $direccion;

    /**
     * @var Municipios $idmun
     * @ORM\ManyToOne(targetEntity="App\Entity\Municipios")
     * @ORM\JoinColumn(name="idmun", referencedColumnName="ID")
     */
    private $idmun;

    /**
     * @ORM\OneToMany(targetEntity=MediosTrans::class, mappedBy="basificacionObj")
     */
    private $medios;

    /**
     * @ORM\ManyToOne(targetEntity=Ramas::class)
     */
    private $rama;

    /**
     * Basificacion constructor.
     */
    public function __construct()
    {
        $this->medios = new ArrayCollection();
        $this->idlbasiam = uniqid();
    }

    /**
     * @return mixed
     */
    public function getMedios()
    {
        return $this->medios;
    }

    /**
     * @param mixed $medios
     * @return Basificacion
     */
    public function setMedios($medios)
    {
        $this->medios = $medios;
        return $this;
    }


    /**
     * Set idlbasiam
     *
     * @param string $idlbasiam
     * @return Basificacion
     */
    public function setIdlbasiam($idlbasiam)
    {
        $this->idlbasiam = $idlbasiam;
    
        return $this;
    }

    /**
     * Get idlbasiam
     *
     * @return string
     */
    public function getIdlbasiam()
    {
        return $this->idlbasiam;
    }

    /**
     * Set idlicencia
     *
     * @param Lotjuridicas $idlicencia
     * @return Basificacion
     */
    public function setIdlicencia($idlicencia)
    {
        $this->idlicencia = $idlicencia;
    
        return $this;
    }

    /**
     * Get idlicencia
     *
     * @return Lotjuridicas
     */
    public function getIdlicencia()
    {
        return $this->idlicencia;
    }

    /**
     * Set nombrelb
     *
     * @param string $nombrelb
     * @return Basificacion
     */
    public function setNombrelb($nombrelb)
    {
        $this->nombrelb = $nombrelb;
    
        return $this;
    }

    /**
     * Get nombrelb
     *
     * @return string 
     */
    public function getNombrelb()
    {
        return $this->nombrelb;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Basificacion
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
     * Set idmun
     *
     * @param Municipios $idmun
     * @return Basificacion
     */
    public function setIdmun(Municipios $idmun)
    {
        $this->idmun = $idmun;
    
        return $this;
    }

    /**
     * Get idmun
     *
     * @return Municipios
     */
    public function getIdmun()
    {
        return $this->idmun;
    }

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return $this->getNombrelb().". ".$this->getDireccion();
    }

    public function addMedio(MediosTrans $medio): self
    {
        if (!$this->medios->contains($medio)) {
            $this->medios[] = $medio;
            $medio->setBasificacionObj($this);
        }

        return $this;
    }

    public function removeMedio(MediosTrans $medio): self
    {
        if ($this->medios->contains($medio)) {
            $this->medios->removeElement($medio);
            // set the owning side to null (unless already changed)
            if ($medio->getBasificacionObj() === $this) {
                $medio->setBasificacionObj(null);
            }
        }

        return $this;
    }

    public function getRama(): ?Ramas
    {
        return $this->rama;
    }

    public function setRama(?Ramas $rama): self
    {
        $this->rama = $rama;

        return $this;
    }
}