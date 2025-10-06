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

    /**
     * Statistiques détaillées par fréquence
     */
    public function getFrequencyStats(): array
    {
        $stats = $this->createQueryBuilder('n')
            ->select('n.frequency, COUNT(n.id) as count')
            ->andWhere('n.isActive = :active')
            ->setParameter('active', true)
            ->groupBy('n.frequency')
            ->getQuery()
            ->getResult();

        $result = ['weekly' => 0, 'monthly' => 0];
        foreach ($stats as $stat) {
            $result[$stat['frequency'] ?? 'weekly'] = (int)$stat['count'];
        }

        return $result;
    }

    /**
     * Évolution des abonnements sur les 12 derniers mois
     */
    public function getSubscriptionTrend(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-{$i} months");
            $startOfMonth = clone $date;
            $startOfMonth->modify('first day of this month 00:00:00');
            $endOfMonth = clone $date;
            $endOfMonth->modify('last day of this month 23:59:59');

            $count = $this->createQueryBuilder('n')
                ->select('COUNT(n.id)')
                ->andWhere('n.subscribedAt >= :start')
                ->andWhere('n.subscribedAt <= :end')
                ->setParameter('start', $startOfMonth)
                ->setParameter('end', $endOfMonth)
                ->getQuery()
                ->getSingleScalarResult();

            $months[] = [
                'month' => $date->format('M Y'),
                'count' => (int)$count
            ];
        }

        return $months;
    }

    /**
     * Top des domaines d'email les plus populaires
     */
    public function getTopEmailDomains(int $limit = 10): array
    {
        $result = $this->createQueryBuilder('n')
            ->select('n.email')
            ->andWhere('n.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();

        $domains = [];
        foreach ($result as $row) {
            $email = $row['email'];
            $domain = substr($email, strpos($email, '@') + 1);
            $domains[$domain] = ($domains[$domain] ?? 0) + 1;
        }

        arsort($domains);
        return array_slice($domains, 0, $limit, true);
    }
}