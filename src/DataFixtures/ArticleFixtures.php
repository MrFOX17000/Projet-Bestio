<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer tous les utilisateurs (pour les rédacteurs)
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($users)) {
            return;
        }

        // Articles prédéfinis sur les animaux et la conservation
        $articles = [
            [
                'titre' => 'Les éléphants d\'Afrique : géants en péril',
                'contenu' => 'Les éléphants d\'Afrique, ces géants majestueux de la savane, font face à des défis sans précédent. Avec leurs défenses imposantes et leur intelligence remarquable, ils jouent un rôle crucial dans l\'écosystème africain. Cependant, le braconnage intensif pour l\'ivoire a décimé leurs populations au cours des dernières décennies.

Aujourd\'hui, il ne reste qu\'environ 415 000 éléphants d\'Afrique dans la nature, contre 1,3 million dans les années 1980. Cette chute drastique s\'explique principalement par le commerce illégal de l\'ivoire, alimenté par une demande persistante en Asie.

Les éléphants sont des "ingénieurs de l\'écosystème" : ils créent des clairières en abattant des arbres, permettant à d\'autres espèces de prospérer. Leurs excréments dispersent des graines sur de vastes distances, contribuant à la régénération forestière. Sans eux, l\'équilibre écologique de nombreuses régions africaines serait bouleversé.

Heureusement, des initiatives prometteuses voient le jour. Les programmes anti-braconnage se renforcent, utilisant des technologies de pointe comme les drones et la surveillance satellitaire. Les communautés locales sont de plus en plus impliquées dans la conservation, créant un cercle vertueux entre protection de la faune et développement économique.

L\'avenir des éléphants d\'Afrique dépend de notre capacité collective à combattre le trafic d\'ivoire et à préserver leurs habitats naturels.',
                'image' => '/uploads/elephants_afrique.jpg'
            ],
            [
                'titre' => 'Le retour spectaculaire du tigre de Sibérie',
                'contenu' => 'Dans les vastes étendues enneigées de la taïga russe, une histoire de conservation remarquable se déroule depuis plusieurs décennies. Le tigre de Sibérie, le plus grand félin du monde, a frôlé l\'extinction dans les années 1940 avec seulement 40 individus recensés.

Grâce à des efforts de conservation acharnés, leur population a atteint aujourd\'hui entre 400 et 500 individus dans la nature. Ce succès extraordinaire résulte d\'une collaboration étroite entre scientifiques russes, organisations internationales et gouvernements.

Ces magnifiques prédateurs peuvent peser jusqu\'à 320 kg et mesurer plus de 3 mètres de long. Leur pelage épais et leurs larges pattes leur permettent de survivre à des températures descendant jusqu\'à -40°C. Ils sont parfaitement adaptés à la chasse dans la neige profonde, traquant principalement des sangliers et des cerfs.

Le territoire d\'un tigre de Sibérie peut s\'étendre sur 400 km², nécessitant de vastes espaces protégés. La Russie a créé plusieurs réserves naturelles spécialement dédiées à leur protection, couvrant des milliers de kilomètres carrés de forêt boréale.

Aujourd\'hui, des programmes de réintroduction s\'étendent à la Chine voisine, où des corridors écologiques sont aménagés pour permettre aux populations de se reconnecter. Cette renaissance du tigre de Sibérie prouve qu\'avec une volonté politique forte et des ressources suffisantes, même les espèces au bord de l\'extinction peuvent se redresser.',
                'image' => '/uploads/tigre_siberie.jpg'
            ],
            [
                'titre' => 'Mystères des profondeurs : les requins-marteaux et leur navigation extraordinaire',
                'contenu' => 'Dans les eaux tropicales du globe, une créature fascinante défie notre compréhension de l\'évolution : le requin-marteau. Sa tête aplatie et élargie, appelée céphalofoil, n\'est pas qu\'une curiosité anatomique, c\'est un véritable prodige de l\'ingénierie naturelle.

Cette forme unique confère au requin-marteau des capacités sensorielles exceptionnelles. Les électrorécepteurs, appelés ampoules de Lorenzini, sont répartis sur toute la surface de sa "tête-marteau", lui permettant de détecter les champs électriques générés par les êtres vivants. Un poisson caché dans le sable n\'a aucune chance d\'échapper à ce radar biologique.

La forme de leur tête améliore également leur vision binoculaire et leur permet d\'effectuer des virages plus serrés que les autres requins. Cette agilité est particulièrement utile lors de la chasse aux raies, leur proie favorite, qu\'ils clouent au sol avec leur tête avant de les dévorer.

Un phénomène fascinant observé chez les requins-marteaux est leur capacité de rassemblement. Près des îles Galápagos et de Cocos, des centaines d\'individus se regroupent en formations spectaculaires, probablement pour la reproduction et le nettoyage par des poissons plus petits.

Ces prédateurs perfectionnés parcourent les océans depuis plus de 20 millions d\'années. Neuf espèces différentes de requins-marteaux peuplent nos mers, du petit requin-marteau nain de 90 cm au grand requin-marteau pouvant atteindre 6 mètres.',
                'image' => '/uploads/requin_marteau.jpg'
            ],
            [
                'titre' => 'Les aigles royaux : maîtres des cieux depuis la nuit des temps',
                'contenu' => 'Dominant les massifs montagneux et les vastes plaines, l\'aigle royal incarne depuis des millénaires la puissance et la liberté. Ce rapace exceptionnel, présent sur quatre continents, fascine par ses capacités de vol et de chasse hors du commun.

Avec une envergure pouvant dépasser 2,3 mètres et une vision huit fois plus précise que celle de l\'homme, l\'aigle royal détecte une proie de la taille d\'un lièvre à plus de 3 kilomètres de distance. Ses serres exercent une pression de 300 kg par cm², suffisante pour broyer les os de ses proies.

Ces maîtres de l\'air construisent des nids gigantesques, appelés aires, qui peuvent atteindre 4 mètres de diamètre et peser plus d\'une tonne. Certaines aires sont utilisées par plusieurs générations successives, enrichies chaque année de nouvelles branches et matériaux.

L\'aigle royal forme des couples monogames qui peuvent durer toute une vie, soit plus de 30 ans. Leur parade nuptiale aérienne est spectaculaire : les partenaires effectuent des vols synchronisés, des piqués vertigineux et des transferts d\'objets en plein vol.

Jadis persécutés par l\'homme qui les considérait comme une menace pour le bétail, les aigles royaux bénéficient aujourd\'hui d\'une protection stricte dans la plupart des pays. Leurs populations se stabilisent progressivement, symbole de la réconciliation possible entre l\'homme et la nature sauvage.',
                'image' => '/uploads/aigle_royal.jpg'
            ],
            [
                'titre' => 'Migrations animales : les voyages les plus épiques de la planète',
                'contenu' => 'Chaque année, des milliards d\'animaux entreprennent des voyages extraordinaires qui défient notre imagination. Ces migrations, véritables prouesses de navigation, représentent l\'un des phénomènes les plus spectaculaires du règne animal.

Le champion toutes catégories reste la sterne arctique, capable de parcourir 70 000 kilomètres par an en suivant l\'été perpétuel entre Arctique et Antarctique. Ce petit oiseau de mer vit ainsi plus de lumière que n\'importe quelle autre créature sur Terre.

Chez les mammifères, les baleines grises détiennent le record avec 20 000 kilomètres aller-retour entre leurs zones d\'alimentation en Alaska et leurs aires de reproduction au Mexique. Ces géants des mers naviguent avec une précision remarquable, utilisant les champs magnétiques terrestres comme boussole naturelle.

Les papillons monarques réalisent l\'une des migrations les plus mystérieuses : quatre générations successives parcourent 4 000 kilomètres entre le Canada et le Mexique, la dernière génération effectuant seule le voyage de retour vers un lieu qu\'elle n\'a jamais vu.

Sur terre, la grande migration du Serengeti voit 2 millions de gnous, zèbres et gazelles suivre les pluies dans un cycle annuel de 800 kilomètres. Ce spectacle grandiose façonne l\'écosystème de toute l\'Afrique de l\'Est.

Ces migrations ancestrales sont aujourd\'hui menacées par le réchauffement climatique et la fragmentation des habitats. Protéger ces couloirs de migration devient crucial pour préserver ces merveilles naturelles.',
                'image' => '/uploads/migration_animale.jpg'
            ],
            [
                'titre' => 'Intelligence animale : quand nos amis à plumes défient nos certitudes',
                'contenu' => 'Longtemps considérés comme de simples automates guidés par l\'instinct, les animaux révèlent chaque jour des capacités cognitives qui remettent en question notre vision du monde. Les oiseaux, en particulier, bousculent nos idées reçues sur l\'intelligence.

Les corvidés (corneilles, corbeaux, pies) possèdent des capacités de résolution de problèmes comparables à celles d\'un enfant de 7 ans. Ils fabriquent des outils, planifient leurs actions et transmettent des connaissances à leur descendance. En Nouvelle-Calédonie, les corneilles façonnent des crochets à partir de feuilles pour extraire des insectes de l\'écorce.

Les perroquets gris du Gabon maîtrisent des concepts abstraits comme la couleur, la forme et le nombre. Alex, le célèbre perroquet d\'Irene Pepperberg, connaissait plus de 100 mots et pouvait répondre à des questions sur des objets jamais vus auparavant.

Plus surprenant encore, certains oiseaux font preuve d\'empathie. Les pies reconnaissent leur reflet dans un miroir, une capacité partagée avec seulement quelques espèces de mammifères supérieurs. Les corbeaux consolent leurs congénères en détresse et peuvent tenir rancune pendant des années.

Cette intelligence aviaire s\'explique par une structure cérébrale unique. Contrairement aux mammifères, les oiseaux ont développé des connexions neuronales denses dans des régions spécifiques, compensant la petite taille de leur cerveau par une efficacité remarquable.

Ces découvertes nous obligent à repenser notre relation avec le monde animal et à reconnaître la richesse cognitive qui nous entoure.',
                'image' => '/uploads/intelligence_oiseaux.jpg'
            ],
            [
                'titre' => 'Sauvetage des espèces : success stories de la conservation moderne',
                'contenu' => 'Dans un monde où le taux d\'extinction s\'accélère, quelques histoires extraordinaires nous rappellent que l\'homme peut aussi être un protecteur. Ces succès de conservation prouvent qu\'avec détermination et moyens appropriés, même les espèces au bord du gouffre peuvent renaître.

Le condor de Californie illustre parfaitement cette renaissance. En 1987, il ne restait que 27 individus de ce géant des airs. Grâce à un programme d\'élevage intensif et de relâcher progressif, plus de 500 condors vivent aujourd\'hui, dont 300 dans la nature. Chaque oiseau porte une puce électronique et fait l\'objet d\'un suivi minutieux.

L\'oryx d\'Arabie a littéralement ressuscité. Déclaré éteint dans la nature en 1972, cette antélope du désert a été reconstituée à partir de quelques individus en captivité. Aujourd\'hui, plus de 1 000 oryx parcourent à nouveau les déserts d\'Arabie, première espèce à passer du statut "éteint dans la nature" à "vulnérable".

Le loup gris de Yellowstone témoigne du pouvoir de la réintroduction. Absent depuis 70 ans, 31 loups canadiens ont été relâchés en 1995. Leur retour a révolutionné l\'écosystème : les populations de cerfs se sont régulées, la végétation s\'est régénérée, et même les cours d\'eau ont changé de tracé.

Ces succès reposent sur des approches scientifiques rigoureuses : élevage en captivité, diversité génétique préservée, préparation minutieuse des sites de réintroduction et implication des communautés locales. Ils prouvent que l\'extinction n\'est pas une fatalité.',
                'image' => '/uploads/conservation_success.jpg'
            ],
            [
                'titre' => 'Face au réchauffement : comment les animaux s\'adaptent-ils ?',
                'contenu' => 'Le changement climatique bouleverse la vie sauvage à un rythme sans précédent. Face à cette crise, les animaux déploient des stratégies d\'adaptation fascinantes, certaines encourageantes, d\'autres inquiétantes pour leur survie future.

L\'ours polaire, symbole du réchauffement arctique, développe de nouveaux comportements alimentaires. Certaines populations se tournent vers les œufs d\'oiseaux, les baies et même les bélugas échoués pour compenser la réduction de leur terrain de chasse sur la banquise. Leurs techniques de pêche évoluent également.

Les oiseaux migrateurs modifient leurs calendriers millénaires. En Europe, de nombreuses espèces avancent leur migration de printemps de 6 à 8 jours par décennie. Certaines renoncent même à migrer, établissant des populations sédentaires dans des régions autrefois trop froides.

Phénomène plus surprenant, certains animaux changent physiquement. L\'éléphant d\'Afrique développe des oreilles plus grandes pour mieux réguler sa température. Plusieurs espèces d\'oiseaux voient leurs becs s\'allonger, facilitant leur thermorégulation.

Les écosystèmes marins subissent des transformations drastiques. L\'acidification des océans affaiblit les coquilles des mollusques, poussant certaines espèces à migrer vers des eaux plus froides. Les coraux développent une tolérance accrue à la chaleur grâce à de nouvelles associations avec des algues symbiotiques.

Ces adaptations révèlent la résilience extraordinaire du vivant, mais aussi ses limites. La vitesse du changement dépasse souvent les capacités d\'adaptation naturelle.',
                'image' => '/uploads/adaptation_climat.jpg'
            ],
            [
                'titre' => 'Venins et poisons : les armes chimiques du règne animal',
                'contenu' => 'Dans la course aux armements qui oppose prédateurs et proies depuis des millions d\'années, la chimie s\'est révélée une arme redoutable. Venins et poisons représentent des chefs-d\'œuvre d\'évolution, alliant efficacité létale et sophistication moléculaire.

Le venin du mamba noir, serpent le plus redouté d\'Afrique, peut tuer un être humain en moins de 20 minutes. Sa neurotoxine bloque la transmission nerveuse, provoquant une paralysie progressive. Paradoxalement, ce même venin contient des molécules aux propriétés analgésiques 200 fois plus puissantes que la morphine.

La pieuvre à anneaux bleus, malgré sa taille modeste, possède suffisamment de venin pour tuer 26 adultes. Sa tetrodotoxine bloque les canaux sodiques, causant une paralysie totale tout en maintenant la victime consciente. Aucun antidote n\'existe à ce jour.

Plus surprenant, la grenouille dorée d\'Amérique du Sud sécrète de la batrachotoxine par sa peau. Une seule grenouille contient assez de poison pour tuer 10 hommes. Les indiens Emberá utilisent depuis des siècles cette substance pour enduire leurs flèches de chasse.

Ces armes biologiques inspirent aujourd\'hui la médecine moderne. Le venin de vipère a donné naissance aux inhibiteurs de l\'enzyme de conversion, révolutionnant le traitement de l\'hypertension. La conotoxine des cônes marins promet de nouveaux antidouleurs sans accoutumance.

L\'étude de ces toxines révèle la complexité fascinante des interactions écologiques et ouvre de nouvelles perspectives thérapeutiques.',
                'image' => '/uploads/venins_animaux.jpg'
            ],
            [
                'titre' => 'Les bâtisseurs du règne animal : architectures naturelles extraordinaires',
                'contenu' => 'Bien avant que l\'homme n\'érige ses premières constructions, le règne animal avait déjà produit des architectes de génie. De la toile d\'araignée aux barrages de castors, ces structures révèlent une ingénierie naturelle d\'une sophistication remarquable.

Les castors, véritables ingénieurs hydrauliques, modifient le paysage à grande échelle. Leurs barrages, pouvant atteindre 850 mètres de long, créent des écosystèmes entiers. Ces structures combinent branches entrelacées, boue et pierres dans un assemblage d\'une résistance exceptionnelle aux crues.

L\'araignée tissserand d\'or produit une soie plus résistante que l\'acier à poids égal. Sa toile orbitale, reconstruite chaque nuit, utilise sept types de soie différents selon leur fonction : support, capture, emballage des proies. L\'industrie textile moderne s\'inspire de cette biotechnologie naturelle.

Les termites africaines érigent des cathédrales souterraines pouvant atteindre 8 mètres de haut. Ces termitières intègrent un système de ventilation sophistiqué maintenant une température constante de 30°C, indépendamment des variations extérieures. Leurs techniques inspirent l\'architecture bioclimatique moderne.

L\'oiseau jardinier satiné construit des "tonnelles" décorées avec un raffinement artistique stupéfiant. Il peint les parois avec de la pulpe de baies, dispose des objets bleus avec un sens esthétique développé, et entretient son œuvre pendant des mois pour séduire les femelles.

Ces créations animales témoignent d\'une intelligence pratique et parfois esthétique qui défie nos préjugés sur le monde sauvage.',
                'image' => '/uploads/architectures_animales.jpg'
            ]
        ];

        // Créer les articles
        foreach ($articles as $index => $articleData) {
            $article = new Article();
            
            $article->setTitreArticle($articleData['titre']);
            $article->setContenuArticle($articleData['contenu']);
            $article->setImage($articleData['image']);
            
            // Assigner un rédacteur aléatoire
            $redacteur = $faker->randomElement($users);
            $article->setRedacteur($redacteur);
            
            // Date de publication entre il y a 3 mois et maintenant
            $datePublication = $faker->dateTimeBetween('-3 months', 'now');
            $article->setDateArticle($datePublication);
            
            $manager->persist($article);
        }

        $manager->flush();

        echo "Created " . count($articles) . " articles for your blog!\n";
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}