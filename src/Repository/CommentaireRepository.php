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
