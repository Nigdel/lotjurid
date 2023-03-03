<?php

namespace App\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
//use App\Entity\Roles;

/**
 * App\Entity\Usuario
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Usuario implements UserInterface
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
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;


    /**
     * @var string $apellidos
     *
     * @ORM\Column(name="apellidos", type="string", length=255)
     */
    private $apellidos;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Roles") */

    public $rol;
    /**
     * @var \DateTime $fecha_alta
     *
     * @ORM\Column(name="fecha_alta", type="datetime")
     */
    private $fecha_alta;


    /**
     * @var integer $idmunicipio
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Municipios")
     * @ORM\JoinColumn(name="idmunicipio", referencedColumnName="ID")
     */
    private $idmunicipio;

    /**
     * @return int
     */
    public function getIdmunicipio()
    {
        return $this->idmunicipio;
    }

    /**
     * @param int $idmunicipio
     */
    public function setIdmunicipio($idmunicipio)
    {
        $this->idmunicipio = $idmunicipio;
    }

    /**
     * @var \DateTime $ultima_conexion
     *
     * @ORM\Column(name="ultima_conexion", type="datetime")
     */
    private $ultima_conexion;

    /**
     * @return \DateTime
     */
    public function getUltimaConexion()
    {
        return $this->ultima_conexion;
    }

    /**
     * @param \DateTime $ultima_conexion
     * @return Usuario
     */
    public function setUltimaConexion($ultima_conexion)
    {
        $this->ultima_conexion = $ultima_conexion;
        return $this;
    }


	public function __construct()
	{
		$this->fecha_alta = new \DateTime();
        $this->ultima_conexion=$this->fecha_alta;

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
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     * @return Usuario
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    
        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    function __sleep()
    {
        return array('apellidos', 'nombre', 'password','id');
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set fecha_alta
     *
     * @param \DateTime $fechaAlta
     * @return Usuario
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fecha_alta = $fechaAlta;
    
        return $this;
    }

    /**
     * Get fecha_alta
     *
     * @return \DateTime 
     */
    public function getFechaAlta()
    {
        return $this->fecha_alta;
    }


    public function __toString()
	{
		return $this->getNombre().' '.$this->getApellidos();
	}
	function eraseCredentials()
	{
		
	}
	function getRoles()
	{
        return array('ROLE_USUARIO');
	}
	function getUsername()
	{
		return $this->getNombre();
	}


    /**
     * Set rol
     *
     * @param Roles $rol
     * @return Usuario
     */
    public function setRol($rol = null)
    {
        $this->rol = $rol;
    
        return $this;
    }

    /**
     * Get rol
     *
     * @return Roles
     */
    public function getRol()
    {
        return $this->rol;
    }



}