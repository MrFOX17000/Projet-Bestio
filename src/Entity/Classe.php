<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use App\Entity\ClasseImage;
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

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @var Collection<int, ClasseImage>
     */
    #[ORM\OneToMany(targetEntity: ClasseImage::class, mappedBy: 'classe', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $classeImages;

    public function __construct()
    {
        $this->dependre = new ArrayCollection();
        $this->classeImages = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        if ($image !== null) {
            $this->image = $image;
        }
        return $this;
    }

    /**
     * @return Collection<int, ClasseImage>
     */
    public function getClasseImages(): Collection
    {
        return $this->classeImages;
    }

    // Alias plus court (ancienne habitude getImages())
    public function getImages(): Collection
    {
        return $this->classeImages;
    }

    public function addClasseImage(ClasseImage $classeImage): static
    {
        if (!$this->classeImages->contains($classeImage)) {
            $this->classeImages->add($classeImage);
            $classeImage->setClasse($this);
        }

        return $this;
    }

    public function removeClasseImage(ClasseImage $classeImage): static
    {
        if ($this->classeImages->removeElement($classeImage)) {
            // set the owning side to null (unless already changed)
            if ($classeImage->getClasse() === $this) {
                $classeImage->setClasse(null);
            }
        }

        return $this;
    }

    public function getImagePaths(): array
    {
        return array_map(fn(ClasseImage $ci) => $ci->getPath(), $this->classeImages->toArray());
    }
}
