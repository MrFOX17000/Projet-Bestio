<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserType;

final class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'connexion')]
    public function connexion(): Response
    {
        $form = $this->createForm(UserType::class);

        return $this->render('security/connexion.html.twig', [
            'form' => $form,
        ]);
    }
}
