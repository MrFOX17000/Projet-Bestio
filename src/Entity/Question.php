<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titreQuestion = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'contenir')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Espece $espece = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'question', orphanRemoval: true)]
    private Collection $posseder;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $poser = null;

    public function __construct()
    {
        $this->posseder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreQuestion(): ?string
    {
        return $this->titreQuestion;
    }

    public function setTitreQuestion(string $titreQuestion): static
    {
        $this->titreQuestion = $titreQuestion;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    /**
     * @return Collection<int, Commentaire>
     */
    public function getPosseder(): Collection
    {
        return $this->posseder;
    }

    public function addPosseder(Commentaire $posseder): static
    {
        if (!$this->posseder->contains($posseder)) {
            $this->posseder->add($posseder);
            $posseder->setQuestion($this);
        }

        return $this;
    }

    public function removePosseder(Commentaire $posseder): static
    {
        if ($this->posseder->removeElement($posseder)) {
            // set the owning side to null (unless already changed)
            if ($posseder->getQuestion() === $this) {
                $posseder->setQuestion(null);
            }
        }

        return $this;
    }

    public function getPoser(): ?User
    {
        return $this->poser;
    }

    public function setPoser(?User $poser): static
    {
        $this->poser = $poser;

        return $this;
    }
}
