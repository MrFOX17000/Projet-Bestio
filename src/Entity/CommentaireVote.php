<?php
namespace App\Entity;

use App\Repository\CommentaireVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireVoteRepository::class)]
#[ORM\Table(name: 'commentaire_vote', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_comment_user', columns: ['commentaire_id','user_id'])
])]
class CommentaireVote
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'votes')] #[ORM\JoinColumn(nullable: false)]
    private ?Commentaire $commentaire = null;

    #[ORM\ManyToOne] #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'smallint')]
    private int $value = 0;

    public function getId(): ?int { return $this->id; }
    public function getCommentaire(): ?Commentaire { return $this->commentaire; }
    public function setCommentaire(Commentaire $c): self { $this->commentaire = $c; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $u): self { $this->user = $u; return $this; }
    public function getValue(): int { return $this->value; }
    public function setValue(int $v): self { $this->value = $v; return $this; }
}