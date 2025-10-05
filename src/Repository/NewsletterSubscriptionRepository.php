<?php

namespace App\Repository;

use App\Entity\NewsletterSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NewsletterSubscription>
 */
class NewsletterSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterSubscription::class);
    }

    /**
     * Trouve tous les abonnés actifs
     */
    public function findActiveSubscriptions(): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('n.subscribedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les abonnés par fréquence
     */
    public function findActiveByFrequency(string $frequency): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.isActive = :active')
            ->andWhere('n.frequency = :frequency')
            ->setParameter('active', true)
            ->setParameter('frequency', $frequency)
            ->orderBy('n.subscribedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un email est déjà abonné
     */
    public function findByEmail(string $email): ?NewsletterSubscription
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve par token de désabonnement
     */
    public function findByToken(string $token): ?NewsletterSubscription
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Statistiques des abonnements
     */
    public function getSubscriptionStats(): array
    {
        $total = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $active = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();

        $thisMonth = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.subscribedAt >= :startOfMonth')
            ->andWhere('n.isActive = :active')
            ->setParameter('startOfMonth', new \DateTime('first day of this month'))
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'thisMonth' => $thisMonth
        ];
    }
}