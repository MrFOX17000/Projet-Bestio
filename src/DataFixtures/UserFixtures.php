<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
    //     $faker = Factory::create('fr_FR');

    //     $avatarFiles = [
    //         '/uploads/avatar/avatar1.png',
    //         '/uploads/avatar/avatar2.png',
    //         '/uploads/avatar/avatar3.png',
    //         '/uploads/avatar/avatar4.png',
    //         '/uploads/avatar/avatar5.png',
    //         '/uploads/avatar/avatar6.png'
    //     ];

    //     // Crée 50 users
    //     $total = 50;
    //     for ($i = 0; $i < $total; $i++) {
    //         $user = new User();
    //         $pseudo = $faker->unique()->userName(); // pseudo unique
    //         $email = $faker->unique()->safeEmail();

    //         $user->setPseudo($pseudo);
    //         $user->setEmail($email);

    //         // roles
    //         $user->setRoles(['ROLE_USER']);

    //         $plain = 'Azertyuiop123?';
    //         $hashed = $this->passwordHasher->hashPassword($user, $plain);
    //         $user->setPassword($hashed);

    //         // isVerified ~60% true
    //         if (method_exists($user, 'setIsVerified')) {
    //             $user->setIsVerified($faker->boolean(60));
    //         }

    //         // banned ~10%
    //         if (method_exists($user, 'setBanned')) {
    //             $user->setBanned($faker->boolean(10));
    //         }

    //         // Photo: 50% chance to have an avatar
    //         if (!empty($avatarFiles) && $faker->boolean(50)) {
    //             // choisir un avatar au hasard
    //             $user->setPhoto($faker->randomElement($avatarFiles));
    //         } else {
    //             // si ta colonne DB autorise null, tu peux laisser null
    //             if (method_exists($user, 'setPhoto')) {
    //                 $user->setPhoto(null);
    //             }
    //         }

    //         $manager->persist($user);
    //     }

    //     $manager->flush();
    }
}
