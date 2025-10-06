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

    public function checkAndUnban(User $user): void //Fonction pour débannir automatiqueemnt un utilisateur
    {
        if ($user->isBanned() && $user->getBannedUntil() !== null) { //on vérifie si l'utilisateur est banni et si la date de fin de ban est définie
            $now = new \DateTime();
            if ($user->getBannedUntil() < $now) { //on compare la date actuelle avec la date de fin de ban
                $user->setBanned(false); //si la date de fin de ban est dépassée, on débannit l'utilisateur
                $user->setBannedUntil(null);
                $this->entityManager->flush();
            }
        }
    }
}
