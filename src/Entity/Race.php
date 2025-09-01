<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomRace = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $specificite = null;

    #[ORM\ManyToOne(inversedBy: 'avoir')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Espece $espece = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomRace(): ?string
    {
        return $this->nomRace;
    }

    public function setNomRace(string $nomRace): static
    {
        $this->nomRace = $nomRace;

        return $this;
    }

    public function getSpecificite(): ?string
    {
        return $this->specificite;
    }

    public function setSpecificite(string $specificite): static
    {
        $this->specificite = $specificite;

        return $this;
    }

    public function getEspece(): ?Espece
    {
        return $this->espece;
    }

    public function setEspece(?Espece $espece): static
    {
        $this->espece = $espece;

        return $this;
    }
}
