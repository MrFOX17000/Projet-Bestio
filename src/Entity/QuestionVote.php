<?php
namespace App\Entity;

use App\Repository\QuestionVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionVoteRepository::class)]
#[ORM\Table(name: 'question_vote', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_question_user', columns: ['question_id','user_id'])
])]
class QuestionVote
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'votes')] #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\ManyToOne] #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'smallint')]
    private int $value = 1; // valeur par défaut cohérente

    public function getId(): ?int { return $this->id; }
    public function getQuestion(): ?Question { return $this->question; }
    public function setQuestion(Question $q): self { $this->question = $q; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $u): self { $this->user = $u; return $this; }
    public function getValue(): int { return $this->value; }
    public function setValue(int $v): self {
        $this->value = $v > 0 ? 1 : -1;
        return $this;
    }
}