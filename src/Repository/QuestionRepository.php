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
     * Trouve les questions populaires pour la newsletter
     */
    public function findPopularQuestions(int $days = 7, int $limit = 3): array
    {
        $date = new \DateTime();
        $date->modify("-$days days");

        return $this->createQueryBuilder('q')
            ->addSelect('author', 'commentaires', 'espece')
            ->leftJoin('q.author', 'author')
            ->leftJoin('q.posseder', 'commentaires')
            ->leftJoin('q.espece', 'espece')
            ->andWhere('q.createdAt >= :date')
            ->setParameter('date', $date)
            ->orderBy('q.createdAt', 'DESC')
            ->setMaxResults($limit)
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

    /**
     * Retourne les questions paginées et triées selon le mode
     */
    public function findPaginatedOrdered(string $mode = 'recent', ?int $userId = null)
    {
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.votes','v')->addSelect('v');

        if ($mode === 'top') {
            $qb->addSelect('COALESCE(SUM(v.value),0) AS HIDDEN score')
               ->groupBy('q.id')
               ->orderBy('score','DESC')
               ->addOrderBy('q.createdAt','DESC');
        } else {
            $qb->orderBy('q.createdAt','DESC');
        }
        return $qb->getQuery()->getResult();
    }

    public function findForEspeceOrdered(int $especeId, string $mode = 'top'): array
    {
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.votes','v')
            ->addSelect('COALESCE(SUM(v.value),0) AS HIDDEN score')
            ->where('q.espece = :e')
            ->setParameter('e',$especeId)
            ->groupBy('q.id');

        if ($mode === 'recent') {
            $qb->orderBy('q.createdAt','DESC');
        } else {
            $qb->orderBy('score','DESC')->addOrderBy('q.createdAt','DESC');
        }
        return $qb->getQuery()->getResult();
    }

    public function findOneWithComments(int $id): ?object
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.votes','vq')->addSelect('vq')
            ->leftJoin('q.commentaires','c')->addSelect('c')
            ->leftJoin('c.votes','vc')->addSelect('vc')
            ->where('q.id = :id')->setParameter('id',$id)
            ->getQuery()->getOneOrNullResult();
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
