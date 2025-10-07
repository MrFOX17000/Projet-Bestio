<?php
// src/Security/UserChecker.php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserChecker implements UserCheckerInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function checkPreAuth(UserInterface $user): void
{
    if (!$user instanceof User) {
        return;
    }

    // Si le bannissement est expiré, on débannit automatiquement
    if ($user->isBanned() && $user->getBannedUntil() && $user->getBannedUntil() < new \DateTime()) {
        $user->setBanned(false);
        $user->setBannedUntil(null);
        $this->em->flush();
    }

    // On ne bloque jamais la connexion ici
}

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien de spécial ici
    }
}
