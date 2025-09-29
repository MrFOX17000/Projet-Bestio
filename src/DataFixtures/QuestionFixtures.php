<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Espece;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
    //     $faker = Factory::create('fr_FR');

    //     // Récupérer tous les utilisateurs
    //     $users = $manager->getRepository(User::class)->findAll();

    //     if (empty($users)) {
    //         return; // Pas d'utilisateurs, on arrête
    //     }

    //     // Questions spécifiques pour chaque espèce
    //     $questionsParEspece = [
    //         'Éléphant d\'Asie' => [
    //             ['Différences avec l\'éléphant d\'Afrique', 'Quelles sont les principales différences entre l\'éléphant d\'Asie et l\'éléphant d\'Afrique ? J\'ai du mal à les distinguer physiquement.'],
    //             ['Intelligence de l\'éléphant d\'Asie', 'Est-ce vrai que les éléphants d\'Asie sont particulièrement intelligents ? Quels comportements le prouvent ?'],
    //             ['Dressage des éléphants', 'Comment se passe le dressage des éléphants d\'Asie en Thaïlande ? Est-ce respectueux de l\'animal ?'],
    //             ['Alimentation quotidienne', 'Combien mange un éléphant d\'Asie par jour ? Quels sont ses aliments préférés ?'],
    //         ],
    //         'Éléphant d\'Afrique' => [
    //             ['Taille des défenses', 'Pourquoi les défenses de l\'éléphant d\'Afrique sont-elles si grandes ? À quoi servent-elles exactement ?'],
    //             ['Braconnage et conservation', 'Quelle est la situation actuelle du braconnage des éléphants d\'Afrique ? Les populations se rétablissent-elles ?'],
    //             ['Vie en troupeau', 'Comment s\'organise un troupeau d\'éléphants d\'Afrique ? Qui dirige le groupe ?'],
    //             ['Migration saisonnière', 'Les éléphants d\'Afrique migrent-ils ? Sur quelles distances se déplacent-ils ?'],
    //         ],
    //         'Vipère' => [
    //             ['Venin de vipère', 'Le venin de vipère est-il mortel pour l\'homme ? Combien de temps a-t-on pour agir après une morsure ?'],
    //             ['Reconnaître une vipère', 'Comment différencier une vipère d\'une couleuvre ? Quels sont les signes distinctifs ?'],
    //             ['Reproduction des vipères', 'Les vipères pondent-elles des œufs ou donnent-elles naissance à des petits vivants ?'],
    //             ['Habitat en France', 'Dans quelles régions de France peut-on rencontrer des vipères ? Où se cachent-elles ?'],
    //         ],
    //         'Tigre du Bengale' => [
    //             ['Territoire du tigre', 'Quelle est la taille du territoire d\'un tigre du Bengale ? Défend-il agressivement son domaine ?'],
    //             ['Chasse nocturne', 'Le tigre du Bengale chasse-t-il plutôt la nuit ? Quelles sont ses techniques de chasse ?'],
    //             ['Tigres blancs du Bengale', 'Existe-t-il vraiment des tigres du Bengale blancs dans la nature ? Ou est-ce uniquement en captivité ?'],
    //             ['Conservation en Inde', 'Combien reste-t-il de tigres du Bengale dans la nature ? Les programmes de protection fonctionnent-ils ?'],
    //         ],
    //         'Tigre de Sibérie' => [
    //             ['Adaptation au froid', 'Comment le tigre de Sibérie résiste-t-il aux températures extrêmes ? Son pelage est-il différent ?'],
    //             ['Taille impressionnante', 'Est-ce vrai que le tigre de Sibérie est le plus grand félin du monde ? Quel poids peut-il atteindre ?'],
    //             ['Proies en Sibérie', 'Que chasse le tigre de Sibérie dans la taïga ? Y a-t-il suffisamment de proies ?'],
    //             ['Population actuelle', 'Combien reste-t-il de tigres de Sibérie ? Leur nombre augmente-t-il ?'],
    //         ],
    //         'Tigre Blanc' => [
    //             ['Génétique du tigre blanc', 'Le tigre blanc est-il une espèce à part ou une mutation génétique ? Comment naît-il ?'],
    //             ['Problèmes de santé', 'Les tigres blancs ont-ils des problèmes de santé liés à leur couleur ? Sont-ils plus fragiles ?'],
    //             ['Existence dans la nature', 'Y a-t-il encore des tigres blancs sauvages ou n\'existent-ils qu\'en captivité ?'],
    //             ['Reproduction en captivité', 'Comment les zoos gèrent-ils la reproduction des tigres blancs ? Y a-t-il de la consanguinité ?'],
    //         ],
    //         'Saumon rouge' => [
    //             ['Migration du saumon', 'Comment le saumon rouge retrouve-t-il sa rivière natale ? Son sens de l\'orientation est incroyable !'],
    //             ['Transformation physique', 'Pourquoi le saumon rouge change-t-il de couleur lors de la reproduction ? Cette transformation est-elle irréversible ?'],
    //             ['Cycle de vie complet', 'Combien de temps vit un saumon rouge ? Meurt-il vraiment après la reproduction ?'],
    //             ['Pêche au saumon', 'La pêche au saumon rouge est-elle réglementée ? Quelles sont les périodes autorisées ?'],
    //         ],
    //         'Requin-tigre' => [
    //             ['Dangerosité du requin-tigre', 'Le requin-tigre est-il vraiment dangereux pour l\'homme ? Combien d\'attaques par an ?'],
    //             ['Régime alimentaire varié', 'Est-ce vrai que le requin-tigre mange de tout ? Trouve-t-on vraiment des objets bizarres dans son estomac ?'],
    //             ['Taille et poids', 'Quelle taille peut atteindre un requin-tigre adulte ? Est-ce le deuxième plus grand requin prédateur ?'],
    //             ['Habitat tropical', 'Dans quelles eaux vit le requin-tigre ? Peut-on le rencontrer en Méditerranée ?'],
    //         ],
    //         'Requin-marteau' => [
    //             ['Forme de la tête', 'Pourquoi le requin-marteau a-t-il cette forme de tête si particulière ? Quel avantage cela lui donne-t-il ?'],
    //             ['Vision et électroception', 'La forme de sa tête améliore-t-elle sa vision ? Comment détecte-t-il ses proies ?'],
    //             ['Bancs de requins-marteaux', 'Est-ce vrai que les requins-marteaux se rassemblent en groupes ? Où peut-on observer ce phénomène ?'],
    //             ['Différentes espèces', 'Combien existe-t-il d\'espèces de requins-marteaux ? Sont-elles toutes marines ?'],
    //         ],
    //         'Grenouille-Taureau' => [
    //             ['Cri caractéristique', 'Pourquoi appelle-t-on cette grenouille "taureau" ? Son cri ressemble-t-il vraiment à un mugissement ?'],
    //             ['Espèce invasive', 'La grenouille-taureau est-elle considérée comme invasive en Europe ? Quel impact sur nos grenouilles locales ?'],
    //             ['Taille impressionnante', 'Quelle taille peut atteindre une grenouille-taureau ? Est-ce la plus grande grenouille d\'Europe ?'],
    //             ['Reproduction massive', 'Combien d\'œufs pond une grenouille-taureau ? Sa reproduction est-elle très prolifique ?'],
    //         ],
    //         'Grenouille Rousse' => [
    //             ['Hibernation', 'Comment la grenouille rousse passe-t-elle l\'hiver ? Où hiberne-t-elle exactement ?'],
    //             ['Ponte précoce', 'Pourquoi la grenouille rousse pond-elle si tôt dans l\'année ? Comment résiste-t-elle au froid ?'],
    //             ['Régime alimentaire', 'Que mange la grenouille rousse ? Chasse-t-elle de jour ou de nuit ?'],
    //             ['Répartition en France', 'Dans quelles régions de France trouve-t-on la grenouille rousse ? Préfère-t-elle la montagne ?'],
    //         ],
    //         'Couleuvre' => [
    //             ['Différence avec la vipère', 'Comment être sûr qu\'il s\'agit d\'une couleuvre et non d\'une vipère ? Quels sont les signes infaillibles ?'],
    //             ['Couleuvres aquatiques', 'Certaines couleuvres vivent-elles dans l\'eau ? Comment nagent-elles ?'],
    //             ['Alimentation des couleuvres', 'Que mangent les couleuvres ? Avalent-elles leurs proies entières ?'],
    //             ['Utilité écologique', 'Quel est le rôle des couleuvres dans l\'écosystème ? Pourquoi ne faut-il pas les tuer ?'],
    //         ],
    //         'Aigle royal' => [
    //             ['Technique de chasse', 'Comment l\'aigle royal chasse-t-il ? Peut-il vraiment s\'attaquer à des proies aussi grosses qu\'un chevreau ?'],
    //             ['Nidification en montagne', 'Où l\'aigle royal construit-il son nid ? Revient-il chaque année au même endroit ?'],
    //             ['Vision exceptionnelle', 'Est-ce vrai que l\'aigle royal voit 8 fois mieux que l\'homme ? Comment fonctionne sa vision ?'],
    //             ['Vitesse en piqué', 'Quelle vitesse peut atteindre un aigle royal en piqué ? Est-il plus rapide que le faucon pèlerin ?'],
    //         ],
    //         'Aigle d\'Australie' => [
    //             ['Adaptation au climat', 'Comment l\'aigle d\'Australie supporte-t-il la chaleur du désert ? A-t-il des adaptations spéciales ?'],
    //             ['Proies typiques', 'Que chasse l\'aigle d\'Australie ? S\'attaque-t-il aux marsupiaux ?'],
    //             ['Différences avec l\'aigle royal', 'Quelles sont les différences entre l\'aigle d\'Australie et l\'aigle royal européen ?'],
    //             ['Répartition sur le continent', 'Dans quelles régions d\'Australie vit cet aigle ? Évite-t-il certaines zones ?'],
    //         ],
    //         'Aigle couronné' => [
    //             ['Couronne distinctive', 'Pourquoi appelle-t-on cet aigle "couronné" ? À quoi ressemble cette couronne de plumes ?'],
    //             ['Habitat africain', 'Dans quelles parties de l\'Afrique vit l\'aigle couronné ? Préfère-t-il la savane ou la forêt ?'],
    //             ['Puissance de serres', 'Est-ce vrai que l\'aigle couronné a les serres les plus puissantes de tous les aigles ?'],
    //             ['Cohabitation avec l\'homme', 'L\'aigle couronné s\'approche-t-il des villages ? Est-il dangereux pour les enfants ?'],
    //         ],
    //         'Aigle botté' => [
    //             ['Plumes sur les pattes', 'Pourquoi l\'aigle botté a-t-il des plumes jusqu\'aux doigts ? Quel avantage cela lui donne-t-il ?'],
    //             ['Migration en Europe', 'L\'aigle botté migre-t-il ? Où passe-t-il l\'hiver ?'],
    //             ['Taille modeste', 'L\'aigle botté est-il vraiment plus petit que les autres aigles ? Quelle est sa taille exacte ?'],
    //             ['Proies de petite taille', 'Que chasse l\'aigle botté ? Se contente-t-il de petites proies comme les lézards ?'],
    //         ],
    //     ];

    //     $totalQuestions = 0;

    //     // Créer 3-5 questions par espèce
    //     foreach ($questionsParEspece as $nomEspece => $questions) {
    //         // Récupérer l'espèce par son nom
    //         $espece = $manager->getRepository(Espece::class)->findOneBy(['nomEspece' => $nomEspece]);
            
    //         if (!$espece) {
    //             continue; // Espèce non trouvée, passer à la suivante
    //         }

    //         // Sélectionner 2 à 4 questions aléatoirement pour cette espèce (max disponible : 4)
    //         $nombreQuestions = $faker->numberBetween(2, min(4, count($questions)));
    //         $questionsSelectionnees = $faker->randomElements($questions, $nombreQuestions);

    //         foreach ($questionsSelectionnees as $questionData) {
    //             $user = $faker->randomElement($users);

    //             $question = new Question();
    //             $question->setTitreQuestion($questionData[0]);
    //             $question->setDescription($questionData[1]);
    //             $question->setEspece($espece);
    //             $question->setAuthor($user);

    //             // Date de création entre il y a 6 mois et maintenant
    //             $createdAt = $faker->dateTimeBetween('-6 months', 'now');
    //             $question->setCreatedAt(\DateTimeImmutable::createFromMutable($createdAt));

    //             // 15% des questions sont verrouillées (modération)
    //             $question->setLocked($faker->boolean(15));

    //             $manager->persist($question);
    //             $totalQuestions++;
    //         }
    //     }

    //     $manager->flush();

    //     echo "Created {$totalQuestions} questions for your species!\n";
    }

    public function getDependencies(): array
     {
         return [
    //         UserFixtures::class,
         ];
 }
}