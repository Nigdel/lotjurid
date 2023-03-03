<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\TipoServAuxCon
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TipoServAuxCon
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $ServAuxConAnexo2
     *
     * @ORM\Column(name="ServAuxConAnexo2", type="string", length=255)
     */
    private $ServAuxConAnexo2;

    /**
     * @var string $ServAuxCon
     *
     * @ORM\Column(name="ServAuxCon", type="string", length=255)
     */
    private $ServAuxCon;

    /**
     * @ORM\OneToMany(targetEntity=Instalaciones::class, mappedBy="servAuxCon")
     */
    private $instalaciones;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idEstNewRes;

    /**
     * @ORM\ManyToOne(targetEntity=Ramas::class)
     */
    private $rama;

    public function __construct()
    {
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
     * Set ServAuxConAnexo2
     *
     * @param string $servAuxConAnexo2
     * @return TipoServAuxCon
     */
    public function setServAuxConAnexo2($servAuxConAnexo2)
    {
        $this->ServAuxConAnexo2 = $servAuxConAnexo2;
    
        return $this;
    }

    /**
     * Get ServAuxConAnexo2
     *
     * @return string 
     */
    public function getServAuxConAnexo2()
    {
        return $this->ServAuxConAnexo2;
    }

    /**
     * Set ServAuxCon
     *
     * @param string $servAuxCon
     * @return TipoServAuxCon
     */
    public function setServAuxCon($servAuxCon)
    {
        $this->ServAuxCon = $servAuxCon;
    
        return $this;
    }

    /**
     * Get ServAuxCon
     *
     * @return string 
     */
    public function getServAuxCon()
    {
        return $this->ServAuxCon;
    }

    public function __toString(){
        return $this->getServAuxCon();
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
            $instalacione->setServAuxCon($this);
        }

        return $this;
    }

    public function removeInstalacione(Instalaciones $instalacione): self
    {
        if ($this->instalaciones->contains($instalacione)) {
            $this->instalaciones->removeElement($instalacione);
            // set the owning side to null (unless already changed)
            if ($instalacione->getServAuxCon() === $this) {
                $instalacione->setServAuxCon(null);
            }
        }

        return $this;
    }

    public function getIdEstNewRes(): ?int
    {
        return $this->idEstNewRes;
    }

    public function setIdEstNewRes(?int $idEstNewRes): self
    {
        $this->idEstNewRes = $idEstNewRes;

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