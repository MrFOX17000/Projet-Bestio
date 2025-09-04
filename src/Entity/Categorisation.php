<?php

namespace App\Entity;

use App\Repository\CategorisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorisationRepository::class)]
class Categorisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCategorisation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $specificite = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\OneToMany(targetEntity: Classe::class, mappedBy: 'appartenir', orphanRemoval: true)]
    private Collection $classes;

    public function __construct()
    {
       
        $this->classes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorisation(): ?string
    {
        return $this->nomCategorisation;
    }

    public function setNomCategorisation(string $nomCategorisation): static
    {
        $this->nomCategorisation = $nomCategorisation;

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



    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setAppartenir($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getAppartenir() === $this) {
                $class->setAppartenir(null);
            }
        }

        return $this;
    }

}
