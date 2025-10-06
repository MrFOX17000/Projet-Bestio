<?php
namespace App\Repository;

use App\Entity\CommentaireVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentaireVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireVote::class);
    }

    public function getScore(int $commentId): int
    {
        return (int)$this->createQueryBuilder('v')
            ->select('COALESCE(SUM(v.value),0)')
            ->where('v.commentaire = :c')->setParameter('c',$commentId)
            ->getQuery()->getSingleScalarResult();
    }
}