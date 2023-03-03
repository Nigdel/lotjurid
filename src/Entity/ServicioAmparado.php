<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\ServicioAmparado
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServicioAmparado
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
     * @var string $ServicioAmparado
     *
     * @ORM\Column(name="ServicioAmparado", type="string", length=30)
     */
    private $ServicioAmparado;

    /**
     * @var string $DescServicioAmparado
     *
     * @ORM\Column(name="DescServicioAmparado", type="string", length=255)
     */
    private $DescServicioAmparado;

    /**
     * @ORM\ManyToOne(targetEntity=Ramas::class)
     */
    private $rama;


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
     * Set ServicioAmparado
     *
     * @param string $servicioAmparado
     * @return ServicioAmparado
     */
    public function setServicioAmparado($servicioAmparado)
    {
        $this->ServicioAmparado = $servicioAmparado;
    
        return $this;
    }

    /**
     * Get ServicioAmparado
     *
     * @return string 
     */
    public function getServicioAmparado()
    {
        return $this->ServicioAmparado;
    }

    /**
     * Set DescServicioAmparado
     *
     * @param string $descServicioAmparado
     * @return ServicioAmparado
     */
    public function setDescServicioAmparado($descServicioAmparado)
    {
        $this->DescServicioAmparado = $descServicioAmparado;
    
        return $this;
    }

    /**
     * Get DescServicioAmparado
     *
     * @return string 
     */
    public function getDescServicioAmparado()
    {
        return $this->DescServicioAmparado;
    }

    public function __toString()
    {
        return $this->getDescServicioAmparado();
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