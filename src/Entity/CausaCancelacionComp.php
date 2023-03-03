<?php

namespace App\Entity;

use App\Repository\CausaCancelacionCompRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CausaCancelacionCompRepository::class)
 */
class CausaCancelacionComp
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $causa;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCausa(): ?string
    {
        return $this->causa;
    }

    public function setCausa(string $causa): self
    {
        $this->causa = $causa;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return $this->getCausa();
    }
}
