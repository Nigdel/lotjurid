<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Moneda
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Moneda
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
     * @var string $Moneda
     *
     * @ORM\Column(name="Moneda", type="string", length=255)
     */
    private $Moneda;


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
     * Set Moneda
     *
     * @param string $moneda
     * @return Moneda
     */
    public function setMoneda($moneda)
    {
        $this->Moneda = $moneda;
    
        return $this;
    }

    /**
     * Get Moneda
     *
     * @return string 
     */
    public function getMoneda()
    {
        return $this->Moneda;
    }

    public function __toString()
    {
        return $this->getMoneda();
    }
}