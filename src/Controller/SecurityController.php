<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use App\Entity\ResetPassword;
use DateTimeImmutable;
use App\Repository\ResetPasswordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SecurityController extends AbstractController
{

    #[Route('/connexion', name: 'connexion')]
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(UserType::class);

        return $this->render('security/connexion.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form,
        ]);
    }

    #[Route('/deconnexion', name: 'deconnexion')]
    public function deconnexion(): void
    {
    }

    #[Route('/reset-password/{token}', name: 'reset-password')]
    public function resetPassword(RateLimiterFactory $passwordRecoveryLimiter, string $token, ResetPasswordRepository $resetPasswordRepository, UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $em): Response
    {
        // $limiter = $passwordRecoveryLimiter->create($request->getClientIp());
        // if (false === $limiter->consume(1)->isAccepted()) {
        //     $this->addFlash('warning', 'Vous avez atteint le nombre maximum de tentatives. Veuillez réessayer dans une heure.');
        //     return $this->redirectToRoute('connexion');
        // }
        $resetPassword = $resetPasswordRepository->findOneBy(['token' => sha1($token)]);
        if (!$resetPassword || $resetPassword->getExpiredAt() < new \DateTime('now')) {
            if($resetPassword) {
                $em->remove($resetPassword);
                $em->flush();
            }
            $this->addFlash('warning', 'Le lien de réinitialisation de mot de passe est invalide ou a expiré.');
            return $this->redirectToRoute('connexion');
        }

        $passwordForm = $this->createFormBuilder()
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe :',
                'attr' => ['placeholder' => 'Entrez votre nouveau mot de passe'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']), 
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/',
                        'message' => 'Le mot de passe doit contenir au minimum une majuscule, une minuscule, un chiffre et 12 caractères dont un caractère spécial',
                    ])
                ]
            ])
            ->getForm();

        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $password = $passwordForm->get('password')->getData();
            $user = $resetPassword->getUser();
            $hash = $userPasswordHasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('connexion');
        }

        return $this->render('security/reset_password_form.html.twig', [
            'form' => $passwordForm->createView()
        ]);
    }

    #[Route('/reset-password-request', name: 'reset-password-request')]
    public function resetPasswordRequest(RateLimiterFactory $passwordRecoveryLimiter, Request $request, UserRepository $userRepository, ResetPasswordRepository $resetPasswordRepository, MailerInterface $mailer, EntityManagerInterface $em): Response
    {   
        // $limiter = $passwordRecoveryLimiter->create($request->getClientIp());
        // if (false === $limiter->consume(1)->isAccepted()) {
        //     $this->addFlash('warning', 'Vous avez atteint le nombre maximum de tentatives. Veuillez réessayer dans une heure.');
        //     return $this->redirectToRoute('connexion');
        // }
        $emailForm = $this->createFormBuilder()->add('email', EmailType::class, [
            'constraints' => [new NotBlank(['message' => 'Veuillez entrer une adresse email'])],
            'label' => 'Votre adresse email :',
            'attr' => ['placeholder' => 'Entrez votre adresse email']
        ])->getForm();

        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $emailValue = $emailForm->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $emailValue]);
            if ($user){
                $oldResetPassword = $resetPasswordRepository->findOneBy(['user' => $user]);
                if ($oldResetPassword) {
                    $em->remove($oldResetPassword);
                    $em->flush();
                }
                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user);
                $resetPassword->setExpiredAt(new \DateTimeImmutable('+1 hour'));
                $token = substr(str_replace(['+', '/', '='], [''], base64_encode(random_bytes(32))), 0, 20);
                $hash = sha1($token);
                $resetPassword->setToken($hash);
                $em->persist($resetPassword);
                $em->flush();
                $email = new TemplatedEmail();
                $baseUrl = $this->getParameter('app.base_url');
                if ($baseUrl) {
                    $path = $this->generateUrl('reset-password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_PATH);
                    $resetUrl = rtrim((string) $baseUrl, '/') . $path;
                } else {
                    $resetUrl = $this->generateUrl('reset-password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                }
                $email->to($emailValue)
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('@emails_templates/reset_password_request.html.twig')
                    ->context([
                        'username' => $user->getPseudo(),
                        'token' => $token,
                        'reset_url' => $resetUrl
                    ]);
                $mailer->send($email);
            }
            $this->addFlash('success', 'Un email de réinitialisation de mot de passe a été envoyé.');
            return $this->redirectToRoute('connexion');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'form' => $emailForm->createView()
        ]);
    }
}

