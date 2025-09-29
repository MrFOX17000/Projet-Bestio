<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $faker = Factory::create('fr_FR');

        // // Récupérer toutes les questions et utilisateurs
        // $questions = $manager->getRepository(Question::class)->findAll();
        // $users = $manager->getRepository(User::class)->findAll();

        // if (empty($questions) || empty($users)) {
        //     return;
        // }

        // // Réponses spécifiques par question ID (basées sur votre export SQL)
        // $reponsesParQuestion = [
        //     6 => [ // Différences éléphant Asie/Afrique
        //         ['L\'éléphant d\'Asie a des oreilles plus petites et une tête plus arrondie. Il n\'a aussi qu\'une seule "pince" au bout de sa trompe, contre deux pour l\'africain.'],
        //         ['Les défenses sont aussi différentes : plus petites chez l\'éléphant d\'Asie, et parfois les femelles n\'en ont pas du tout !'],
        //         ['Question de taille aussi, l\'éléphant d\'Afrique est généralement plus grand et plus massif.'],
        //     ],
        //     7 => [ // Dressage éléphants Thaïlande
        //         ['Malheureusement le dressage traditionnel utilise souvent des méthodes cruelles... Le "phajaan" (brisage de l\'esprit) est encore pratiqué.'],
        //         ['Il existe heureusement des sanctuaires éthiques qui utilisent le renforcement positif plutôt que la violence.'],
        //         ['Je recommande de bien se renseigner avant de visiter un centre avec éléphants en Thaïlande.'],
        //     ],
        //     8 => [ // Braconnage éléphants Afrique
        //         ['La situation s\'améliore lentement. Les populations ont cessé de chuter drastiquement dans certaines régions grâce aux efforts de conservation.'],
        //         ['Mais le braconnage reste un fléau majeur. Environ 20 000 éléphants sont encore tués chaque année pour l\'ivoire.'],
        //         ['Les programmes anti-braconnage et la sensibilisation locale donnent des résultats encourageants au Kenya et en Tanzanie.'],
        //     ],
        //     9 => [ // Troupeau éléphants
        //         ['C\'est la matriarche, la femelle la plus âgée, qui dirige le troupeau ! Elle a l\'expérience et la mémoire des points d\'eau.'],
        //         ['Les mâles quittent le groupe vers 12-15 ans et vivent souvent seuls ou en petits groupes de célibataires.'],
        //         ['Fascinant de voir comment elles protègent les petits au centre du groupe lors des déplacements.'],
        //     ],
        //     10 => [ // Défenses éléphant Afrique
        //         ['Les défenses servent à creuser, déraciner, se défendre et marquer le territoire. Plus elles sont grandes, plus le mâle est dominant.'],
        //         ['Malheureusement cette caractéristique en fait des cibles privilégiées pour les braconniers...'],
        //         ['Il y a une évolution génétique observée : de plus en plus d\'éléphants naissent sans défenses !'],
        //     ],
        //     11 => [ // Migration éléphants
        //         ['Ils peuvent parcourir des centaines de kilomètres selon les saisons, suivant les pluies et la nourriture.'],
        //         ['Ces "couloirs de migration" ancestraux sont cruciaux mais souvent bloqués par l\'urbanisation.'],
        //         ['Au Botswana, on a observé des migrations de plus de 500 km !'],
        //     ],
        //     12 => [ // Reproduction vipères
        //         ['Les vipères sont ovovivipares : elles gardent les œufs dans leur corps et donnent naissance à des petits vivants.'],
        //         ['Contrairement aux couleuvres qui pondent des œufs dans la terre ou sous des pierres.'],
        //         ['Une portée peut compter 5 à 20 petits selon l\'espèce et la taille de la femelle.'],
        //     ],
        //     13 => [ // Venin vipère
        //         ['Le venin de vipère européenne est rarement mortel pour un adulte en bonne santé, mais il faut consulter rapidement !'],
        //         ['Sérum antivenimeux disponible dans les hôpitaux des zones à risque. Pas de garrot, pas de succion !'],
        //         ['Les enfants et personnes âgées sont plus à risque. Symptômes : douleur, gonflement, nausées.'],
        //     ],
        //     14 => [ // Conservation tigre Bengale
        //         ['Il reste environ 2500 tigres du Bengale dans la nature, principalement en Inde.'],
        //         ['Les réserves comme Ranthambore montrent que la protection fonctionne : leurs populations remontent !'],
        //         ['Projet Tigre en Inde : succès mitigé mais encourageant sur le long terme.'],
        //     ],
        //     15 => [ // Tigres blancs Bengale
        //         ['Les tigres blancs sauvages sont probablement éteints. Le dernier observé dans la nature date de 1958.'],
        //         ['C\'est une mutation récessive rare. En captivité, ils viennent souvent de consanguinité...'],
        //         ['Tous les tigres blancs actuels descendent d\'un mâle capturé en Inde en 1951 : Mohan.'],
        //     ],
        //     16 => [ // Chasse tigre Bengale
        //         ['Le tigre chasse principalement au crépuscule et la nuit. Vision nocturne 6 fois meilleure que l\'homme !'],
        //         ['Technique d\'embuscade : approche silencieuse puis bond puissant de 10m.'],
        //         ['Morsure à la gorge pour les gros mammifères, nuque pour les plus petites proies.'],
        //     ],
        //     17 => [ // Territoire tigre
        //         ['Un mâle peut contrôler 60 à 100 km² selon la densité de proies. Les femelles ont des territoires plus petits.'],
        //         ['Ils marquent avec l\'urine, les griffures et les phéromones. Très territorial !'],
        //         ['Conflits violents entre mâles lors de la saison de reproduction.'],
        //     ],
        //     18 => [ // Taille tigre Sibérie
        //         ['C\'est effectivement le plus grand félin ! Les mâles peuvent peser jusqu\'à 320 kg.'],
        //         ['Record documenté : 384 kg pour un mâle en captivité. Dans la nature, moyenne autour de 180-200 kg.'],
        //         ['Leur taille les aide à survivre aux hivers sibériens rigoureux.'],
        //     ],
        //     19 => [ // Proies tigre Sibérie
        //         ['Principalement des sangliers et cerfs sika. Parfois des ours bruns !'],
        //         ['Un tigre peut consommer 9-10 kg de viande par jour. Une proie lui dure plusieurs jours.'],
        //         ['La déforestation réduit les populations de proies, forçant les tigres à s\'approcher des villages.'],
        //     ],
        //     20 => [ // Population tigre Sibérie
        //         ['Environ 400-500 individus dans la nature, principalement en Russie.'],
        //         ['Population stable voire en légère augmentation grâce aux efforts de conservation russes.'],
        //         ['Programme de réintroduction en Chine avec des individus russes.'],
        //     ],
        //     21 => [ // Génétique tigre blanc
        //         ['C\'est une mutation récessive du gène qui contrôle la couleur. Il faut deux parents porteurs.'],
        //         ['Pas une espèce différente ! Juste une variation de couleur du tigre du Bengale.'],
        //         ['La consanguinité en captivité cause souvent des problèmes : strabisme, malformations...'],
        //     ],
        //     22 => [ // Reproduction tigres blancs
        //         ['Beaucoup de zoos ont arrêté car cela nécessite trop de consanguinité pour maintenir la couleur blanche.'],
        //         ['Association américaine des zoos décourage cette pratique depuis les années 2000.'],
        //         ['Mieux vaut se concentrer sur la conservation des tigres sauvages normaux.'],
        //     ],
        //     23 => [ // Tigres blancs nature
        //         ['Dernier tigre blanc sauvage tué en 1958 en Inde. Depuis, uniquement en captivité.'],
        //         ['Dans la nature, cette couleur est un handicap : moins de camouflage pour chasser.'],
        //         ['Quelques témoignages non vérifiés en Inde récemment, mais rien de confirmé.'],
        //     ],
        //     24 => [ // Transformation saumon
        //         ['Les hormones de reproduction changent complètement leur physiologie ! Rouge vif, machoires crochues...'],
        //         ['Cette transformation est irréversible. Leur corps se "consume" littéralement pour la reproduction.'],
        //         ['Les femelles deviennent aussi agressives pour protéger leurs nids.'],
        //     ],
        //     25 => [ // Cycle vie saumon
        //         ['La plupart meurent effectivement après la reproduction, épuisés. Cycle de 2-8 ans selon l\'espèce.'],
        //         ['Quelques individus survivent et retournent en mer pour un second cycle (rare).'],
        //         ['Leurs corps morts nourrissent tout l\'écosystème : ours, aigles, forêt...'],
        //     ],
        //     26 => [ // Régime requin-tigre
        //         ['Surnommé "poubelle des mers" ! On a trouvé des plaques d\'immatriculation, des pneus, des bouteilles...'],
        //         ['Mais ils mangent surtout poissons, phoques, tortues, raies. Opportunistes total !'],
        //         ['Dents en forme de scie parfaites pour découper n\'importe quoi.'],
        //     ],
        //     27 => [ // Habitat requin-tigre
        //         ['Eaux tropicales et subtropicales chaudes. Pas de Méditerranée, trop froide en hiver.'],
        //         ['Océan Indien, Pacifique, Atlantique. Préfère les eaux côtières peu profondes.'],
        //         ['Migrations saisonnières vers les eaux plus chaudes en hiver.'],
        //     ],
        //     28 => [ // Vision requin-marteau
        //         ['Sa tête élargie lui donne un champ de vision de presque 360° ! Vision binoculaire excellente.'],
        //         ['Les électrorécepteurs sont répartis sur toute sa "tête-marteau" pour détecter les proies cachées.'],
        //         ['Peut détecter les battements de cœur d\'une raie enterrée dans le sable !'],
        //     ],
        //     29 => [ // Forme tête requin-marteau
        //         ['La forme améliore l\'hydrodynamisme et la manœuvrabilité. Comme un aileron avant de F1 !'],
        //         ['Concentration maximale des organes sensoriels pour la détection électrique.'],
        //         ['Évolution remarquable : plusieurs espèces ont développé cette forme indépendamment.'],
        //     ],
        //     30 => [ // Espèces requins-marteaux
        //         ['Il existe 9 espèces de requins-marteaux ! Du grand requin-marteau (6m) au petit (90cm).'],
        //         ['Tous marins, mais certains remontent les estuaires. Aucun en eau douce pure.'],
        //         ['Le requin-marteau halicorne a la forme la plus extrême.'],
        //     ],
        //     31 => [ // Grenouille-taureau invasive
        //         ['Très invasive en Europe ! Elle mange nos grenouilles locales et leurs têtards.'],
        //         ['Originaire d\'Amérique du Nord, introduite pour l\'élevage. Erreur écologique majeure.'],
        //         ['Programmes d\'éradication en cours mais très difficile à éliminer complètement.'],
        //     ],
        //     32 => [ // Reproduction grenouille-taureau
        //         ['Une femelle peut pondre jusqu\'à 20 000 œufs par an ! Reproduction explosive.'],
        //         ['Les têtards peuvent rester 2-3 ans dans l\'eau avant la métamorphose.'],
        //         ['Cette capacité reproductive explique son succès invasif.'],
        //     ],
        //     33 => [ // Ponte grenouille rousse
        //         ['Elle pond dès février-mars, souvent avec de la glace encore ! Résistance incroyable au froid.'],
        //         ['Les œufs sont protégés par une masse gélatineuse qui isole du froid.'],
        //         ['Avantage évolutif : moins de prédateurs actifs en sortie d\'hiver.'],
        //     ],
        //     34 => [ // Hibernation grenouille rousse
        //         ['Elle hiberne dans la vase au fond des mares, métabolisme quasi-arrêté.'],
        //         ['Peut aussi hiberner sous des tas de feuilles humides ou des pierres.'],
        //         ['Survit même si la mare gèle en surface grâce à l\'oxygène dissous.'],
        //     ],
        //     35 => [ // Utilité couleuvres
        //         ['Prédateur naturel des rongeurs ! Une couleuvre mange des dizaines de souris par an.'],
        //         ['Régulation écologique cruciale. Sans elles, explosion des populations de rongeurs.'],
        //         ['Aussi : mangent limaces, escargots nuisibles aux jardins.'],
        //     ],
        //     36 => [ // Couleuvres aquatiques
        //         ['La couleuvre à collier nage très bien ! Chasse grenouilles et poissons.'],
        //         ['La couleuvre vipérine est quasi-aquatique, souvent confondue avec une vipère.'],
        //         ['Elles ondulent verticalement dans l\'eau, contrairement aux serpents de mer.'],
        //     ],
        //     37 => [ // Différence couleuvre/vipère
        //         ['Pupilles rondes chez la couleuvre, verticales chez la vipère.'],
        //         ['Tête triangulaire et cou marqué chez la vipère, tête ovale chez la couleuvre.'],
        //         ['Écailles : carénées (rugueuses) chez la vipère, lisses chez la couleuvre.'],
        //     ],
        //     38 => [ // Alimentation couleuvres
        //         ['Elles avalent leurs proies entières ! Mâchoires disloquables.'],
        //         ['Régime : rongeurs, oiseaux, œufs, amphibiens selon l\'espèce.'],
        //         ['Digestion lente : peuvent jeûner plusieurs semaines après un gros repas.'],
        //     ],
        //     39 => [ // Nidification aigle royal
        //         ['Nids énormes sur les falaises ! Peuvent atteindre 2m de diamètre et être utilisés des décennies.'],
        //         ['Couple fidèle à vie, revient au même territoire mais peut avoir plusieurs nids.'],
        //         ['Construisent avec des branches, tapissent de mousse et herbes sèches.'],
        //     ],
        //     40 => [ // Vitesse aigle royal
        //         ['En piqué : jusqu\'à 240 km/h ! Mais le faucon pèlerin reste le champion à 380 km/h.'],
        //         ['Vol de croisière autour de 50-80 km/h selon les courants thermiques.'],
        //         ['Leur puissance compense leur vitesse moindre : serres terrifiantes !'],
        //     ],
        //     41 => [ // Vision aigle royal
        //         ['Vision 4-8 fois plus précise que l\'homme ! Ils voient une souris à 1,5 km.'],
        //         ['Deux fovéas par œil (nous n\'en avons qu\'une) pour vision frontale ET latérale nette.'],
        //         ['Perception UV : voient les traces d\'urine des rongeurs invisibles pour nous !'],
        //     ],
        //     42 => [ // Aigle Australie répartition
        //         ['Présent sur tout le continent ! Évite juste les déserts les plus arides du centre.'],
        //         ['Préfère les zones ouvertes : savanes, prairies, zones semi-arides.'],
        //         ['Populations stables contrairement à beaucoup d\'autres rapaces australiens.'],
        //     ],
        //     43 => [ // Adaptation aigle Australie chaleur
        //         ['Halètement comme les chiens ! Et position ailes ouvertes pour l\'ombre.'],
        //         ['Activité réduite aux heures les plus chaudes, chasse tôt le matin.'],
        //         ['Plumage plus clair que les aigles européens pour réfléchir la chaleur.'],
        //     ],
        //     44 => [ // Habitat aigle couronné
        //         ['Forêts denses d\'Afrique subsaharienne principalement. Chasseur forestier.'],
        //         ['Du Sénégal à l\'Afrique du Sud, évite les zones désertiques.'],
        //         ['Préfère la canopée dense où sa puissance compense dans les espaces restreints.'],
        //     ],
        //     45 => [ // Couronne aigle couronné
        //         ['Longues plumes noires dressées sur la nuque ! Spectaculaire quand il est excité.'],
        //         ['Cette "couronne" se dresse lors des parades ou face à une menace.'],
        //         ['Les jeunes ont une couronne moins développée, blanchâtre.'],
        //     ],
        //     46 => [ // Serres aigle couronné
        //         ['Serres proportionnellement énormes ! Peut s\'attaquer à des singes de 30 kg.'],
        //         ['Pression de 750 PSI dans ses serres, de quoi broyer des os.'],
        //         ['Spécialisé dans la chasse aux primates : technique redoutable.'],
        //     ],
        //     47 => [ // Cohabitation aigle couronné
        //         ['Évite généralement l\'homme mais peut être dangereux si provoqué.'],
        //         ['Quelques cas documentés d\'attaques sur de petits enfants en Afrique.'],
        //         ['Respecté et craint par les populations locales, nombreuses légendes.'],
        //     ],
        //     48 => [ // Proies aigle botté
        //         ['Spécialisé dans les petites proies : lézards, serpents, petits mammifères.'],
        //         ['Chasse aussi oiseaux de taille moyenne et parfois gros insectes.'],
        //         ['Sa taille modeste l\'oblige à être opportuniste et varié dans son régime.'],
        //     ],
        //     49 => [ // Plumes pattes aigle botté
        //         ['Protection contre les morsures de serpents ! Comme des jambières naturelles.'],
        //         ['Isolation thermique aussi, utile lors des migrations vers l\'Europe du Nord.'],
        //         ['Caractéristique des aigles "vrais" (genre Aquila) versus autres rapaces.'],
        //     ],
        //     50 => [ // Migration aigle botté
        //         ['Grand migrateur ! Hiverne en Afrique tropicale, niche en Europe/Asie.'],
        //         ['Migration spectaculaire par Gibraltar et Bosphore, milliers d\'individus !'],
        //         ['Voyage de 10 000 km aller-retour chaque année.'],
        //     ],
        // ];

        // $totalCommentaires = 0;

        // foreach ($questions as $question) {
        //     $questionId = $question->getId();
            
        //     // Vérifier si on a des réponses prédéfinies pour cette question
        //     if (!isset($reponsesParQuestion[$questionId])) {
        //         continue;
        //     }

        //     $reponsesPossibles = $reponsesParQuestion[$questionId];
            
        //     // Créer 1 à 3 commentaires par question
        //     $nombreCommentaires = $faker->numberBetween(1, min(3, count($reponsesPossibles)));
        //     $reponsesSelectionnees = $faker->randomElements($reponsesPossibles, $nombreCommentaires);

        //     foreach ($reponsesSelectionnees as $index => $reponseData) {
        //         $user = $faker->randomElement($users);

        //         $commentaire = new Commentaire();
        //         $commentaire->setContenu($reponseData[0]);
        //         $commentaire->setQuestion($question);
        //         $commentaire->setAuthor($user);

        //         // Date de création après la question
        //         $questionDate = $question->getCreatedAt();
        //         $minDate = $questionDate->format('Y-m-d H:i:s');
        //         $maxDate = 'now';
                
        //         $createdAt = $faker->dateTimeBetween($minDate, $maxDate);
        //         $commentaire->setCreatedAtComm(\DateTimeImmutable::createFromMutable($createdAt));

        //         $manager->persist($commentaire);
        //         $totalCommentaires++;
        //     }
        // }

        // $manager->flush();

        // echo "Created {$totalCommentaires} comments for your questions!\n";
    }

    public function getDependencies(): array
    {
         return [
        //     UserFixtures::class,
        //     QuestionFixtures::class,
         ];
    }
}