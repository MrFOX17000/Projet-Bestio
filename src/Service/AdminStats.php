<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

final class AdminStats
{
    public function __construct(private ManagerRegistry $doctrine) {}

    public function getStats(): array
    {
        return [
            'users_total'      => $this->countBy('App\Entity\User'),
            'users_admins'     => $this->countAdmins(),
            'users_banned'     => $this->countBy('App\Entity\User', ['banned' => true]),
            'questions_total'  => $this->countBy('App\Entity\Question'),
            'comments_total'   => $this->countFirstExisting([
                'App\Entity\Comment',
                'App\Entity\Commentaire',
                'App\Entity\Response',
                'App\Entity\Reponse',
            ]),
            'classes_total'    => $this->countBy('App\Entity\Classe'),
            'species_total'    => $this->countBy('App\Entity\Espece'),
            'categories_total' => $this->countBy('App\Entity\Categorisation'),
        ];
    }

    private function countBy(string $fqcn, array $criteria = []): int
    {
        if (!class_exists($fqcn)) {
            return 0;
        }
        $em = $this->getEm($fqcn);
        if (!$em) {
            return 0;
        }

        $repo = $em->getRepository($fqcn);
        // La plupart de tes repositories étendent ServiceEntityRepository => count() existe
        if (method_exists($repo, 'count')) {
            return (int) $repo->count($criteria);
        }

        // Fallback DQL générique (sans critères avancés)
        $alias = 'e';
        $dql = "SELECT COUNT($alias.id) FROM $fqcn $alias";
        return (int) $em->createQuery($dql)->getSingleScalarResult();
    }

    private function countAdmins(): int
    {
        $fqcn = 'App\Entity\User';
        if (!class_exists($fqcn)) {
            return 0;
        }
        $em = $this->getEm($fqcn);
        if (!$em) {
            return 0;
        }
        // Colonne roles stocke du JSON -> LIKE marche dans la plupart des configs
        $alias = 'u';
        return (int) $em->createQueryBuilder()
            ->select("COUNT($alias.id)")
            ->from($fqcn, $alias)
            ->where("$alias.roles LIKE :role")
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function countFirstExisting(array $classes): int
    {
        foreach ($classes as $fqcn) {
            if (class_exists($fqcn)) {
                return $this->countBy($fqcn);
            }
        }
        return 0;
    }

    private function getEm(string $fqcn): ?EntityManagerInterface
    {
        $om = $this->doctrine->getManagerForClass($fqcn);
        return $om instanceof EntityManagerInterface ? $om : null;
    }
}