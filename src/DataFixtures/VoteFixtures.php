<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Question;
use App\Entity\Commentaire;
use App\Entity\QuestionVote;
use App\Entity\CommentaireVote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $users       = $manager->getRepository(User::class)->findAll();
        $questions   = $manager->getRepository(Question::class)->findAll();
        $commentaires= $manager->getRepository(Commentaire::class)->findAll();

        if (count($users) < 2) {
            return; // pas assez d’utilisateurs
        }

        // Votes sur QUESTIONS
        foreach ($questions as $question) {
            $nbVotes = $faker->numberBetween(0, 8);
            if ($nbVotes === 0) {
                continue;
            }

            // sous-ensemble unique d’utilisateurs
            $pool = $faker->randomElements($users, min($nbVotes, count($users)));

            foreach ($pool as $user) {
                // éviter vote auteur
                if (method_exists($question, 'getAuthor') && $question->getAuthor() === $user) {
                    continue;
                }

                $vote = new QuestionVote();
                $vote->setQuestion($question)
                     ->setUser($user)
                     ->setValue($faker->biasedNumberBetween(-1, 1, function($x){ return $x * $x; }) >= 0
                         ? (mt_rand(0,100) < 70 ? 1 : -1)  // 70% positifs
                         : -1);

                // sécurité valeur (1 ou -1)
                if ($vote->getValue() === 0) {
                    $vote->setValue(1);
                }
                $manager->persist($vote);
            }
        }

        // Votes sur COMMENTAIRES
        foreach ($commentaires as $commentaire) {
            $nbVotes = $faker->numberBetween(0, 6);
            if ($nbVotes === 0) {
                continue;
            }

            $pool = $faker->randomElements($users, min($nbVotes, count($users)));

            foreach ($pool as $user) {
                if (method_exists($commentaire, 'getAuthor') && $commentaire->getAuthor() === $user) {
                    continue;
                }

                $vote = new CommentaireVote();
                $vote->setCommentaire($commentaire)
                     ->setUser($user)
                     ->setValue(mt_rand(0,100) < 65 ? 1 : -1); // 65% positifs

                $manager->persist($vote);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Ajuste les noms selon tes classes réelles
        return [
            UserFixtures::class,
            QuestionFixtures::class,
            CommentaireFixtures::class,
        ];
    }
}