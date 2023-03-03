<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Provincias
 *
 * @ORM\Table(name="provincias")
 * @ORM\Entity
 */
class Provincias
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $sGuid
     *
     * @ORM\Column(name="s_GUID", type="string", length=50, nullable=true)
     */
    private $sGuid;

    /**
     * @var string $provincias
     *
     * @ORM\Column(name="Provincias", type="string", length=50, nullable=true)
     */
    private $provincias;

    /**
     * @ORM\OneToMany(targetEntity=DireccionProvincial::class, mappedBy="provincia")
     */
    private $direccionProvincials;

    public function __construct()
    {
        $this->direccionProvincials = new ArrayCollection();
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
     * @return Provincias
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
     * Set provincias
     *
     * @param string $provincias
     * @return Provincias
     */
    public function setProvincias($provincias)
    {
        $this->provincias = $provincias;
    
        return $this;
    }

    /**
     * Get provincias
     *
     * @return string 
     */
    public function getProvincias()
    {
        return $this->provincias;
    }

    public function __toString(){
        return $this->provincias;
    }

    /**
     * @return Collection|DireccionProvincial[]
     */
    public function getDireccionProvincials(): Collection
    {
        return $this->direccionProvincials;
    }

    public function addDireccionProvincial(DireccionProvincial $direccionProvincial): self
    {
        if (!$this->direccionProvincials->contains($direccionProvincial)) {
            $this->direccionProvincials[] = $direccionProvincial;
            $direccionProvincial->setProvincia($this);
        }

        return $this;
    }

    public function removeDireccionProvincial(DireccionProvincial $direccionProvincial): self
    {
        if ($this->direccionProvincials->contains($direccionProvincial)) {
            $this->direccionProvincials->removeElement($direccionProvincial);
            // set the owning side to null (unless already changed)
            if ($direccionProvincial->getProvincia() === $this) {
                $direccionProvincial->setProvincia(null);
            }
        }

        return $this;
    }
}