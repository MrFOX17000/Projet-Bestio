<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Espece;
use App\Entity\Question;
use App\Repository\ArticleRepository;
use App\Repository\EspeceRepository;
use App\Repository\NewsletterSubscriptionRepository;
use App\Repository\QuestionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class NewsletterService
{
    public function __construct(
        private MailerInterface $mailer,
        private NewsletterSubscriptionRepository $subscriptionRepository,
        private ArticleRepository $articleRepository,
        private QuestionRepository $questionRepository,
        private EspeceRepository $especeRepository
    ) {}

    /**
     * Génère le contenu de la newsletter
     */
    public function generateNewsletterContent(): array
    {
        // Articles récents (derniers 7 jours)
        $recentArticles = $this->articleRepository->findRecentArticles(7, 5);

        // Questions populaires (derniers 7 jours)
        $popularQuestions = $this->questionRepository->findPopularQuestions(7, 3);

        // Espèce mise en avant (aléatoire)
        $featuredSpecies = $this->especeRepository->findRandomSpecies(1);

        // Statistiques du site
        $stats = [
            'totalSpecies' => $this->especeRepository->count([]),
            'totalArticles' => $this->articleRepository->count([]),
            'totalQuestions' => $this->questionRepository->count([]),
            'subscribers' => $this->subscriptionRepository->getSubscriptionStats()['active']
        ];

        return [
            'recentArticles' => $recentArticles,
            'popularQuestions' => $popularQuestions,
            'featuredSpecies' => $featuredSpecies[0] ?? null,
            'stats' => $stats,
            'date' => new \DateTime()
        ];
    }

    /**
     * Envoie la newsletter à tous les abonnés actifs
     */
    public function sendNewsletter(string $frequency = 'weekly'): int
    {
        $subscribers = $this->subscriptionRepository->findActiveByFrequency($frequency);
        $content = $this->generateNewsletterContent();
        $sentCount = 0;

        foreach ($subscribers as $subscription) {
            try {
                $email = (new TemplatedEmail())
                    ->from(new Address('newsletter@bestio.com', 'Bestio Newsletter'))
                    ->to($subscription->getEmail())
                    ->subject('🐾 Newsletter Bestio - Découvertes de la semaine')
                    ->htmlTemplate('emails/newsletter_dynamic.html.twig')
                    ->context([
                        'subscription' => $subscription,
                        'content' => $content,
                        'unsubscribeUrl' => 'https://bestio.com/newsletter/unsubscribe/' . $subscription->getToken()
                    ]);

                $this->mailer->send($email);
                $sentCount++;
            } catch (\Exception $e) {
                // Log l'erreur mais continue l'envoi pour les autres
                error_log("Erreur envoi newsletter pour {$subscription->getEmail()}: " . $e->getMessage());
            }
        }

        return $sentCount;
    }

    /**
     * Envoie un email de bienvenue à un nouvel abonné
     */
    public function sendWelcomeEmail(string $email, string $token): void
    {
        $welcomeEmail = (new TemplatedEmail())
            ->from(new Address('newsletter@bestio.com', 'Bestio'))
            ->to($email)
            ->subject('🎉 Bienvenue dans la communauté Bestio !')
            ->htmlTemplate('emails/newsletter_welcome.html.twig')
            ->context([
                'recipientEmail' => $email,
                'unsubscribeUrl' => 'https://bestio.com/newsletter/unsubscribe/' . $token
            ]);

        $this->mailer->send($welcomeEmail);
    }

    /**
     * Envoie un email de test de newsletter
     */
    public function sendTestNewsletter(string $email): void
    {
        // Contenu de test statique pour éviter les erreurs
        $testContent = [
            'recentArticles' => [
                (object)[
                    'titreArticle' => 'Les secrets de la communication chez les dauphins',
                    'contenuArticle' => 'Les dauphins possèdent un système de communication complexe qui fascine les scientifiques. Découvrez comment ces mammifères marins utilisent les ultrasons pour communiquer entre eux et naviguer dans leur environnement aquatique.',
                    'id' => 1
                ],
                (object)[
                    'titreArticle' => 'Migration des oiseaux : un phénomène extraordinaire',
                    'contenuArticle' => 'Chaque année, des milliards d\'oiseaux parcourent des milliers de kilomètres. Comment s\'orientent-ils ? Quels sont les défis qu\'ils rencontrent lors de ces voyages épiques ?',
                    'id' => 2
                ]
            ],
            'popularQuestions' => [
                (object)[
                    'titre' => 'Comment identifier un serpent venimeux ?',
                    'auteur' => (object)['pseudo' => 'naturelover42'],
                    'dateQuestion' => new \DateTime('2025-10-02'),
                    'id' => 1
                ],
                (object)[
                    'titre' => 'Pourquoi les chats ronronnent-ils ?',
                    'auteur' => (object)['pseudo' => 'félinophile'],
                    'dateQuestion' => new \DateTime('2025-09-30'),
                    'id' => 2
                ]
            ],
            'featuredSpecies' => (object)[
                'nomCommun' => 'Gecko Léopard',
                'nomScientifique' => 'Eublepharis macularius',
                'description' => 'Ce petit reptile nocturne originaire d\'Afghanistan et du Pakistan est devenu l\'un des lézards les plus populaires en terrariophilie grâce à son tempérament docile et ses magnifiques motifs.',
                'id' => 1
            ],
            'stats' => [
                'totalSpecies' => 1247,
                'totalArticles' => 89,
                'totalQuestions' => 342,
                'subscribers' => 156
            ],
            'date' => new \DateTime()
        ];

        // Créer un objet subscription simple pour le test
        $testSubscription = new \stdClass();
        $testSubscription->email = $email;

        $testEmail = (new TemplatedEmail())
            ->from(new Address('newsletter@bestio.com', 'Bestio Newsletter'))
            ->to($email)
            ->subject('🐾 [TEST] Newsletter Bestio - Découvertes de la semaine')
            ->htmlTemplate('emails/newsletter.html.twig')
            ->context([
                'subscription' => $testSubscription,
                'content' => $testContent,
                'unsubscribeUrl' => 'https://bestio.com/newsletter/unsubscribe/test-token-123'
            ]);

        $this->mailer->send($testEmail);
    }
}