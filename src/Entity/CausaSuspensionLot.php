<?php

namespace App\Entity;

use App\Repository\CausaSuspencionLotRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CausaSuspencionLotRepository::class)
 */
class CausaSuspensionLot
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
}
