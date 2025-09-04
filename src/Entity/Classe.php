<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $specificite = null;

    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorisation $appartenir = null;

    /**
     * @var Collection<int, Espece>
     */
    #[ORM\OneToMany(targetEntity: Espece::class, mappedBy: 'classe', orphanRemoval: true)]
    private Collection $dependre;

    public function __construct()
    {
        $this->dependre = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getAppartenir(): ?Categorisation
    {
        return $this->appartenir;
    }

    public function setAppartenir(?Categorisation $appartenir): static
    {
        $this->appartenir = $appartenir;

        return $this;
    }

    /**
     * @return Collection<int, Espece>
     */
    public function getDependre(): Collection
    {
        return $this->dependre;
    }

    public function addDependre(Espece $dependre): static
    {
        if (!$this->dependre->contains($dependre)) {
            $this->dependre->add($dependre);
            $dependre->setClasse($this);
        }

        return $this;
    }

    public function removeDependre(Espece $dependre): static
    {
        if ($this->dependre->removeElement($dependre)) {
            // set the owning side to null (unless already changed)
            if ($dependre->getClasse() === $this) {
                $dependre->setClasse(null);
            }
        }

        return $this;
    }
}
