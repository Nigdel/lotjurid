<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Roles
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Roles
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
     * @var string $rol
     *
     * @ORM\Column(name="rol", type="string", length=100)
     */
    private $rol;

    /**
     * @var integer $permisos
     *
     * @ORM\Column(name="permisos", type="integer")
     */
    private $permisos;


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
     * Set rol
     *
     * @param string $rol
     * @return Roles
     */
    public function setRol($rol)
    {
        $this->rol = $rol;
    
        return $this;
    }

    /**
     * Get rol
     *
     * @return string 
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Set permisos
     *
     * @param integer $permisos
     * @return Roles
     */
    public function setPermisos($permisos)
    {
        $this->permisos = $permisos;
    
        return $this;
    }

    /**
     * Get permisos
     *
     * @return integer 
     */
    public function getPermisos()
    {
        return $this->permisos;
    }

    public function __toString()
    {
       return $this->getRol();
    }

}