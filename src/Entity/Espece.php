<?php

namespace App\Entity;

use App\Repository\EspeceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EspeceRepository::class)]
class Espece
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomEspece = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $tailleMoy = null;

    #[ORM\Column]
    private ?int $poidsMoy = null;

    #[ORM\Column]
    private ?int $gestation = null;

    #[ORM\Column]
    private ?int $esperanceVie = null;

    #[ORM\Column(length: 255)]
    private ?string $habitat = null;

    #[ORM\Column(length: 255)]
    private ?string $alimentation = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'espece', orphanRemoval: true)]
    private Collection $contenir;

    #[ORM\ManyToOne(inversedBy: 'dependre')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classe $classe = null;

    public function __construct()
    {
        $this->contenir = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEspece(): ?string
    {
        return $this->nomEspece;
    }

    public function setNomEspece(string $nomEspece): static
    {
        $this->nomEspece = $nomEspece;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTailleMoy(): ?float
    {
        return $this->tailleMoy;
    }

    public function setTailleMoy(float $tailleMoy): static
    {
        $this->tailleMoy = $tailleMoy;

        return $this;
    }

    public function getPoidsMoy(): ?int
    {
        return $this->poidsMoy;
    }

    public function setPoidsMoy(int $poidsMoy): static
    {
        $this->poidsMoy = $poidsMoy;

        return $this;
    }

    public function getGestation(): ?int
    {
        return $this->gestation;
    }

    public function setGestation(int $gestation): static
    {
        $this->gestation = $gestation;

        return $this;
    }

    public function getEsperanceVie(): ?int
    {
        return $this->esperanceVie;
    }

    public function setEsperanceVie(int $esperanceVie): static
    {
        $this->esperanceVie = $esperanceVie;

        return $this;
    }

    public function getHabitat(): ?string
    {
        return $this->habitat;
    }

    public function setHabitat(string $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getAlimentation(): ?string
    {
        return $this->alimentation;
    }

    public function setAlimentation(string $alimentation): static
    {
        $this->alimentation = $alimentation;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    
    /**
     * @return Collection<int, Question>
     */
    public function getContenir(): Collection
    {
        return $this->contenir;
    }

    public function addContenir(Question $contenir): static
    {
        if (!$this->contenir->contains($contenir)) {
            $this->contenir->add($contenir);
            $contenir->setEspece($this);
        }

        return $this;
    }

    public function removeContenir(Question $contenir): static
    {
        if ($this->contenir->removeElement($contenir)) {
            // set the owning side to null (unless already changed)
            if ($contenir->getEspece() === $this) {
                $contenir->setEspece(null);
            }
        }

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

}
