<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BanManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkAndUnban(User $user): void
    {
        if ($user->isBanned() && $user->getBannedUntil() !== null) {
            $now = new \DateTime();
            if ($user->getBannedUntil() < $now) {
                $user->setBanned(false);
                $user->setBannedUntil(null);
                $this->entityManager->flush();
            }
        }
    }
}
