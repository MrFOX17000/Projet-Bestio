<?php
namespace App\Repository;

use App\Entity\QuestionVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionVote::class);
    }

    public function getScore(int $questionId): int
    {
        return (int)$this->createQueryBuilder('v')
            ->select('COALESCE(SUM(v.value),0)')
            ->where('v.question = :q')->setParameter('q',$questionId)
            ->getQuery()->getSingleScalarResult();
    }
}