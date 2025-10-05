<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use App\Repository\EspeceRepository;
use App\Repository\NewsletterSubscriptionRepository;
use App\Repository\QuestionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class TestEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private ArticleRepository $articleRepository,
        private QuestionRepository $questionRepository,
        private EspeceRepository $especeRepository,
        private NewsletterSubscriptionRepository $subscriptionRepository
    ) {}

    /**
     * Envoie un email de newsletter de test avec des données dynamiques
     */
    public function sendTestNewsletter(string $email): void
    {
        // Génération des données dynamiques pour le test
        $content = $this->generateTestContent();
        
        // Création d'un objet subscription factice pour le test
        $testSubscription = (object) [
            'email' => $email,
            'frequency' => 'weekly'
        ];

        $testEmail = (new TemplatedEmail())
            ->from(new Address('newsletter@bestio.com', 'Bestio Newsletter'))
            ->to($email)
            ->subject('🐾 [TEST] Newsletter Bestio - Découvertes de la semaine')
            ->htmlTemplate('emails/newsletter_dynamic.html.twig')
            ->context([
                'subscription' => $testSubscription,
                'content' => $content,
                'unsubscribeUrl' => 'https://bestio.com/newsletter/unsubscribe/test-token-123'
            ]);

        $this->mailer->send($testEmail);
    }

    /**
     * Génère le contenu de test pour la newsletter
     */
    private function generateTestContent(): array
    {
        // Articles récents (derniers 30 jours pour avoir plus de contenu)
        $recentArticles = $this->articleRepository->findRecentArticles(30, 3);

        // Questions populaires (derniers 30 jours)
        $popularQuestions = $this->questionRepository->findPopularQuestions(30, 2);

        // Espèce aléatoire mise en avant
        $featuredSpecies = $this->especeRepository->findRandomSpecies(1);

        // Statistiques du site
        $stats = [
            'totalSpecies' => $this->especeRepository->count([]),
            'totalArticles' => $this->articleRepository->count([]),
            'totalQuestions' => $this->questionRepository->count([]),
            'subscribers' => $this->subscriptionRepository->getSubscriptionStats()['active'] ?? 0
        ];

        return [
            'recentArticles' => $recentArticles,
            'popularQuestions' => $popularQuestions,
            'featuredSpecies' => $featuredSpecies[0] ?? null,
            'stats' => $stats,
            'date' => new \DateTime()
        ];
    }
}