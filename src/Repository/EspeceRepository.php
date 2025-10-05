<?php

namespace App\Repository;

use App\Entity\Espece;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Espece>
 */
class EspeceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Espece::class);
    }

public function findEspecesWithQuestions(): array
{
    return $this->createQueryBuilder('e')
        ->innerJoin('e.contenir', 'q')
        ->groupBy('e.id')
        ->getQuery()
        ->getResult();
}

/**
 * Trouve des espèces aléatoires pour la newsletter
 */
public function findRandomSpecies(int $limit = 1): array
{
    $totalCount = $this->createQueryBuilder('e')
        ->select('COUNT(e.id)')
        ->getQuery()
        ->getSingleScalarResult();
    
    if ($totalCount == 0) {
        return [];
    }
    
    $offset = max(0, rand(0, $totalCount - $limit));
    
    return $this->createQueryBuilder('e')
        ->addSelect('c')
        ->leftJoin('e.classe', 'c')
        ->setMaxResults($limit)
        ->setFirstResult($offset)
        ->getQuery()
        ->getResult();
}


}
