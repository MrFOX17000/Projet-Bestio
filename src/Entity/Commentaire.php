<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentaireRepository;
use App\Entity\CommentaireVote;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAtComm = null;

    #[ORM\ManyToOne(inversedBy: 'posseder')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\ManyToOne(inversedBy: 'ecrire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'commentaire', targetEntity: CommentaireVote::class, orphanRemoval: true, cascade: ['remove'])]
    private Collection $votes;

    public function __construct()
    {
        $this->createdAtComm = new DateTimeImmutable();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCreatedAtComm(): ?\DateTimeImmutable
    {
        return $this->createdAtComm;
    }

    public function setCreatedAtComm(\DateTimeImmutable $createdAtComm): static
    {
        $this->createdAtComm = $createdAtComm;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /** @return Collection<int,CommentaireVote> */
    public function getVotes(): Collection { return $this->votes; }

    public function getScore(): int {
        $s = 0; foreach ($this->votes as $v) { $s += $v->getValue(); }
        return $s;
    }

    public function getUserVote(?User $user): ?int {
        if(!$user) return null;
        foreach ($this->votes as $v) {
            if ($v->getUser() === $user) return $v->getValue();
        }
        return null;
    }

    public function removeVote(CommentaireVote $vote): self {
        $this->votes->removeElement($vote);
        return $this;
    }
}
