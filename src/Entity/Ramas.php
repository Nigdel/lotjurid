<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Ramas
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Ramas
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
     * @var string $Ramas
     *
     * @ORM\Column(name="Ramas", type="string", length=255)
     */
    private $Ramas;

    /**
     * @var string $RamasAnexo2
     *
     * @ORM\Column(name="RamasAnexo2", type="string", length=255)
     */
    private $RamasAnexo2;


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
     * Set Ramas
     *
     * @param string $ramas
     * @return Ramas
     */
    public function setRamas($ramas)
    {
        $this->Ramas = $ramas;
    
        return $this;
    }

    /**
     * Get Ramas
     *
     * @return string 
     */
    public function getRamas()
    {
        return $this->Ramas;
    }

    /**
     * Set RamasAnexo2
     *
     * @param string $ramasAnexo2
     * @return Ramas
     */
    public function setRamasAnexo2($ramasAnexo2)
    {
        $this->RamasAnexo2 = $ramasAnexo2;
    
        return $this;
    }

    /**
     * Get RamasAnexo2
     *
     * @return string 
     */
    public function getRamasAnexo2()
    {
        return $this->RamasAnexo2;
    }

    public function __toString()
    {
        return $this->getRamas();
    }
}