<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Retourne le nombre de questions par espece (tableau [espece_id => count])
     */
    public function countQuestionsByEspece(): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.espece) AS espece_id, COUNT(q.id) AS question_count')
            ->groupBy('q.espece');
        $result = $qb->getQuery()->getResult();
        $counts = [];
        foreach ($result as $row) {
            $counts[$row['espece_id']] = (int)$row['question_count'];
        }
        return $counts;
    }

    /**
     * Retourne les questions d'une espèce avec l'auteur (fetch join)
     */
    public function findByEspeceWithAuthor(int $especeId): array
    {
        return $this->createQueryBuilder('q')
            ->addSelect('author')
            ->innerJoin('q.espece', 'e')
            ->leftJoin('q.author', 'author')
            ->andWhere('e.id = :eid')
            ->setParameter('eid', $especeId)
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne une question par id avec son auteur et son espèce (fetch join)
     */
    public function findOneWithAuthorAndEspece(int $id): ?Question
    {
        return $this->createQueryBuilder('q')
            ->addSelect('author', 'e')
            ->leftJoin('q.author', 'author')
            ->leftJoin('q.espece', 'e')
            ->andWhere('q.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Question[] Returns an array of Question objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('q.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Question
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
