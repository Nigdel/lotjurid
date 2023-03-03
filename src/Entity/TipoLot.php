<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\TipoLot
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TipoLot
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
     * @var string $tipodelot
     *
     * @ORM\Column(name="tipodelot", type="string",length=20)
     */
    private $tipodelot;


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
     * Set tipodelot
     *
     * @param string $tipodelot
     * @return TipoLot
     */
    public function setTipodelot($tipodelot)
    {
        $this->tipodelot = $tipodelot;
    
        return $this;
    }

    /**
     * Get tipodelot
     *
     * @return string
     */
    public function getTipodelot()
    {
        return $this->tipodelot;
    }

    public function __toString()
    {
        return $this->getTipodelot();
    }
}