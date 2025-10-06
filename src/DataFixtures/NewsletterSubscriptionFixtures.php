<?php

namespace App\DataFixtures;

use App\Entity\NewsletterSubscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NewsletterSubscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Abonnements d'utilisateurs existants
        $this->createUserSubscriptions($manager);
        
        // Abonnements d'emails externes (non-utilisateurs)
        $this->createGuestSubscriptions($manager);
        
        $manager->flush();
    }

    private function createUserSubscriptions(ObjectManager $manager): void
    {
        // Récupérer quelques utilisateurs existants
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->findAll();
        
        // Abonner environ 60% des utilisateurs à la newsletter
        $subscribedUsers = array_slice($users, 0, (int)(count($users) * 0.6));
        
        $frequencies = ['weekly', 'monthly'];
        
        foreach ($subscribedUsers as $index => $user) {
            $subscription = new NewsletterSubscription();
            $subscription->setEmail($user->getUserIdentifier());
            $subscription->setUser($user);
            $subscription->setFrequency($frequencies[array_rand($frequencies)]);
            
            // Simuler des dates d'inscription différentes (derniers 6 mois)
            $daysAgo = rand(1, 180);
            $subscribedAt = new \DateTime();
            $subscribedAt->modify("-{$daysAgo} days");
            $subscription->setSubscribedAt($subscribedAt);
            
            // Quelques utilisateurs désabonnés (10%)
            if ($index % 10 === 0) {
                $subscription->setIsActive(false);
            }
            
            $manager->persist($subscription);
        }
    }

    private function createGuestSubscriptions(ObjectManager $manager): void
    {
        // Emails d'invités (personnes pas encore inscrites sur le site)
        $guestEmails = [
            'marie.dubois@email.fr',
            'jean.martin@gmail.com',
            'sophie.bernard@yahoo.fr',
            'lucas.petit@outlook.com',
            'emma.robert@protonmail.com',
            'pierre.durand@free.fr',
            'julie.moreau@orange.fr',
            'nicolas.simon@laposte.net',
            'celine.laurent@hotmail.com',
            'thomas.michel@sfr.fr',
            'amelie.leroy@bbox.fr',
            'antoine.garcia@wanadoo.fr',
            'camille.roux@gmail.com',
            'maxime.vincent@live.com',
            'sarah.fournier@icloud.com',
            'alex.morel@tutanota.com',
            'claire.girard@gmx.fr',
            'hugo.andre@mail.com',
            'lisa.mercier@yandex.com',
            'kevin.blanchard@zoho.com',
            // Quelques emails d'universités et institutions
            'recherche.animale@sorbonne.fr',
            'etude.biodiversite@cnrs.fr',
            'conservation@wwf.org',
            'education@zoobeauval.com',
            'veterinaure@clinique-animaux.fr',
            // Quelques emails de professionnels
            'contact@elevage-reptiles.com',
            'info@aquarium-tropical.fr',
            'admin@refuge-sauvage.org',
            'biologiste@museum-histoire.fr',
            'photographe@nature-passion.com'
        ];

        $frequencies = ['weekly', 'monthly'];

        foreach ($guestEmails as $index => $email) {
            $subscription = new NewsletterSubscription();
            $subscription->setEmail($email);
            $subscription->setUser(null); // Pas d'utilisateur associé
            $subscription->setFrequency($frequencies[array_rand($frequencies)]);
            
            // Simuler des dates d'inscription différentes
            $daysAgo = rand(1, 365); // Jusqu'à 1 an
            $subscribedAt = new \DateTime();
            $subscribedAt->modify("-{$daysAgo} days");
            $subscription->setSubscribedAt($subscribedAt);
            
            // Quelques abonnements désactivés (15% pour les guests)
            if ($index % 7 === 0) {
                $subscription->setIsActive(false);
            }
            
            $manager->persist($subscription);
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class, // Assure-toi que les utilisateurs sont créés avant
        ];
    }
}