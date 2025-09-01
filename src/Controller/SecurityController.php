<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'connexion')]
    public function connexion(): Response
    {
        return $this->render('security/connexion.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }
}
