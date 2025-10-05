<?php

namespace App\Controller;

use App\Entity\NewsletterSubscription;
use App\Form\NewsletterSubscriptionType;
use App\Repository\NewsletterSubscriptionRepository;
use App\Service\NewsletterService;
use App\Service\TestEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/newsletter')]
class NewsletterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NewsletterSubscriptionRepository $subscriptionRepository
    ) {}

    #[Route('/subscribe', name: 'newsletter_subscribe')]
    public function subscribe(Request $request): Response
    {
        $subscription = new NewsletterSubscription();
        
        // Si l'utilisateur est connecté, pré-remplir avec son email
        if ($this->getUser()) {
            $subscription->setEmail($this->getUser()->getUserIdentifier());
            $subscription->setUser($this->getUser());
        }

        $form = $this->createForm(NewsletterSubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email n'est pas déjà abonné
            $existingSubscription = $this->subscriptionRepository->findByEmail($subscription->getEmail());
            
            if ($existingSubscription) {
                if ($existingSubscription->isActive()) {
                    $this->addFlash('warning', 'Cet email est déjà abonné à notre newsletter.');
                } else {
                    // Réactiver l'abonnement existant
                    $existingSubscription->setIsActive(true);
                    $existingSubscription->setFrequency($subscription->getFrequency());
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Votre abonnement à la newsletter a été réactivé !');
                }
            } else {
                $this->entityManager->persist($subscription);
                $this->entityManager->flush();
                $this->addFlash('success', 'Merci ! Vous êtes maintenant abonné(e) à notre newsletter Bestio.');
            }

            return $this->redirectToRoute('newsletter_subscribe');
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form->createView(),
            'stats' => $this->subscriptionRepository->getSubscriptionStats()
        ]);
    }

    #[Route('/unsubscribe/{token}', name: 'newsletter_unsubscribe')]
    public function unsubscribe(string $token): Response
    {
        $subscription = $this->subscriptionRepository->findByToken($token);

        if (!$subscription) {
            throw $this->createNotFoundException('Token de désabonnement invalide.');
        }

        $subscription->setIsActive(false);
        $this->entityManager->flush();

        $this->addFlash('info', 'Vous avez été désabonné(e) de notre newsletter. Nous espérons vous revoir bientôt !');

        return $this->render('newsletter/unsubscribed.html.twig', [
            'email' => $subscription->getEmail()
        ]);
    }

    #[Route('/preview', name: 'newsletter_preview')]
    public function preview(): Response
    {
        // Simple aperçu statique pour éviter les bugs
        return $this->render('newsletter/preview.html.twig');
    }

    #[Route('/test-email', name: 'newsletter_test_email')]
    public function testEmail(TestEmailService $testEmailService): Response
    {
        // Cette route est uniquement pour les admins
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        try {
            // Envoyer un email de test à l'utilisateur connecté
            $testEmail = $this->getUser()->getUserIdentifier();
            $testEmailService->sendTestNewsletter($testEmail);
            
            $this->addFlash('success', "Email de test envoyé à {$testEmail}. Vérifiez votre boîte Mailtrap !");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi : ' . $e->getMessage());
        }

        return $this->redirectToRoute('newsletter_preview');
    }
}