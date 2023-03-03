<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\TipoAuxCon
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TipoAuxCon
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
     * @var string $TipoAuxCon
     *
     * @ORM\Column(name="TipoAuxCon", type="string", length=255)
     */
    private $TipoAuxCon;


    public function __construct()
    {

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
     * Set TipoAuxCon
     *
     * @param string $tipoAuxCon
     * @return TipoAuxCon
     */
    public function setTipoAuxCon($tipoAuxCon)
    {
        $this->TipoAuxCon = $tipoAuxCon;
    
        return $this;
    }

    /**
     * Get TipoAuxCon
     *
     * @return string 
     */
    public function getTipoAuxCon()
    {
        return $this->TipoAuxCon;
    }
    public function __toString()
    {
        return $this->getTipoAuxCon();
    }

}