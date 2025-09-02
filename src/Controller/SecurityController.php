<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'connexion')]
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(UserType::class);

        return $this->render('security/connexion.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/deconnexion', name: 'deconnexion')]
    public function deconnexion(): void
    {
    }
}
