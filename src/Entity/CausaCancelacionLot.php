<?php

namespace App\Entity;

use App\Repository\CausaCancelacionLotRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CausaCancelacionLotRepository::class)
 */
class CausaCancelacionLot
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clegal;

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

    public function getClegal(): ?string
    {
        return $this->clegal;
    }

    public function setClegal(?string $clegal): self
    {
        $this->clegal = $clegal;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->causa;
    }
}
