<?php

namespace App\Repository;

use App\Entity\Classe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classe>
 */
class ClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classe::class);
    }

    public function findAllWithCategorie(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.appartenir', 'cat')
            ->addSelect('cat')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithCategorieAndEspeces(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.appartenir', 'cat')
            ->addSelect('cat')
            ->leftJoin('c.dependre', 'espece')
            ->addSelect('espece')
            ->getQuery()
            ->getResult();
    }

    public function findOneWithRelations(string $nom): ?Classe
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.appartenir', 'cat')->addSelect('cat')
            ->leftJoin('c.dependre', 'e')->addSelect('e')
            ->where('c.nom = :nom')
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchByName(string $search, $categorieId)
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.nom LIKE :search')
                ->andWhere('c.appartenir = :categorie')
                ->setParameter('search', '%' . $search . '%')
                ->setParameter('categorie', $categorieId)
                ->getQuery()
                ->getResult();
        }



    //    /**
    //     * @return Classe[] Returns an array of Classe objects
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

    //    public function findOneBySomeField($value): ?Classe
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
