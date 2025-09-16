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


}
