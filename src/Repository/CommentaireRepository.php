<?php

namespace App\Repository;

use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    /**
     * Retourne les commentaires d'une question avec leurs auteurs (fetch join)
     */
    public function findByQuestionWithAuthor(int $questionId): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('author')
            ->innerJoin('c.question', 'q')
            ->leftJoin('c.author', 'author')
            ->andWhere('q.id = :qid')
            ->setParameter('qid', $questionId)
            ->orderBy('c.createdAtComm', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findForQuestionOrdered(int $questionId, string $mode = 'top'): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.votes', 'v')
            ->addSelect('COALESCE(SUM(v.value),0) AS HIDDEN score')
            ->where('c.question = :q')
            ->setParameter('q', $questionId)
            ->groupBy('c.id');

        if ($mode === 'recent') {
            $qb->orderBy('c.createdAtComm', 'DESC');
        } else {
            $qb->orderBy('score', 'DESC')->addOrderBy('c.createdAtComm', 'DESC');
        }
        return $qb->getQuery()->getResult();
    }

    public function getScore(int $commentId): int
    {
        return (int)$this->createQueryBuilder('c')
            ->leftJoin('c.votes', 'v')
            ->select('COALESCE(SUM(v.value),0)')
            ->where('c.id = :id')->setParameter('id', $commentId)
            ->getQuery()->getSingleScalarResult();
    }

    //    /**
    //     * @return Commentaire[] Returns an array of Commentaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commentaire
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
