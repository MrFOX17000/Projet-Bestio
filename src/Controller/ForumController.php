<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(): Response
    {
        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }

    #[Route('/forum/{nom_espece}', name: 'app_forum_show')]
    public function show(string $nom_espece): Response
    {
        return $this->render('forum/espece.html.twig', [
            'controller_name' => 'ForumController',
            'nom_espece' => $nom_espece,
        ]);
    }

    #[Route('/forum/{nom_espece}/question/{id}', name: 'app_forum_question_show')]
    public function showQuestion(string $nom_espece, int $id): Response
    {
        return $this->render('forum/question.html.twig', [
            'controller_name' => 'ForumController',
            'nom_espece' => $nom_espece,
            'question_id' => $id,
        ]);
    }
}
