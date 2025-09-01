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
     * @var Collection<int, Espece>
     */
    #[ORM\OneToMany(targetEntity: Espece::class, mappedBy: 'appartenir', orphanRemoval: true)]
    private Collection $especes;

    public function __construct()
    {
        $this->especes = new ArrayCollection();
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
     * @return Collection<int, Espece>
     */
    public function getEspeces(): Collection
    {
        return $this->especes;
    }

    public function addEspece(Espece $espece): static
    {
        if (!$this->especes->contains($espece)) {
            $this->especes->add($espece);
            $espece->setAppartenir($this);
        }

        return $this;
    }

    public function removeEspece(Espece $espece): static
    {
        if ($this->especes->removeElement($espece)) {
            // set the owning side to null (unless already changed)
            if ($espece->getAppartenir() === $this) {
                $espece->setAppartenir(null);
            }
        }

        return $this;
    }

}
