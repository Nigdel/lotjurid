<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ExtensionRepository;

/**
 * App\Entity\Extension
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass=ExtensionRepository::class)
 */

class Extension
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
     * @var string $Extension
     *
     * @ORM\Column(name="Extension", type="string", length=255)
     */
    private $Extension;

    /**
     * @var string $ExtensionAnexo2
     *
     * @ORM\Column(name="ExtensionAnexo2", type="string", length=255)
     */
    private $ExtensionAnexo2;


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
     * Set Extension
     *
     * @param string $extension
     * @return Extension
     */
    public function setExtension($extension)
    {
        $this->Extension = $extension;
    
        return $this;
    }

    /**
     * Get Extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->Extension;
    }

    /**
     * Set ExtensionAnexo2
     *
     * @param string $extensionAnexo2
     * @return Extension
     */
    public function setExtensionAnexo2($extensionAnexo2)
    {
        $this->ExtensionAnexo2 = $extensionAnexo2;
    
        return $this;
    }

    /**
     * Get ExtensionAnexo2
     *
     * @return string 
     */
    public function getExtensionAnexo2()
    {
        return $this->ExtensionAnexo2;
    }

    public function __toString()
    {
        return $this->getExtension();
    }
}