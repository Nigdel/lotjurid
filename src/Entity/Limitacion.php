<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Limitacion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Limitacion
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
     * @var string $Limitacion
     *
     * @ORM\Column(name="Limitacion", type="string", length=255)
     */
    private $Limitacion;

    /**
     * @var string $DescLimitacion
     *
     * @ORM\Column(name="DescLimitacion", type="string", length=255)
     */
    private $DescLimitacion;


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
     * Set Limitacion
     *
     * @param string $limitacion
     * @return Limitacion
     */
    public function setLimitacion($limitacion)
    {
        $this->Limitacion = $limitacion;
    
        return $this;
    }

    /**
     * Get Limitacion
     *
     * @return string 
     */
    public function getLimitacion()
    {
        return $this->Limitacion;
    }

    /**
     * Set DescLimitacion
     *
     * @param string $descLimitacion
     * @return Limitacion
     */
    public function setDescLimitacion($descLimitacion)
    {
        $this->DescLimitacion = $descLimitacion;
    
        return $this;
    }

    /**
     * Get DescLimitacion
     *
     * @return string 
     */
    public function getDescLimitacion()
    {
        return $this->DescLimitacion;
    }

    public function __toString()
    {
        return $this->getDescLimitacion();
    }
}