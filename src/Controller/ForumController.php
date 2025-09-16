<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Repository\EspeceRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function show(string $nom_espece, QuestionRepository $questionRepository, EspeceRepository $especeRepository): Response
    {
        $espece = $especeRepository->findOneBy(['nomEspece' => $nom_espece]);

        if (!$espece) {
            $this->addFlash('warning', 'L\'espèce demandée n\'existe pas.');
            return $this->redirectToRoute('app_forum');
        }

        $questions = $questionRepository->findBy(['espece' => $espece]);
        if (!$questions) {
            $this->addFlash('info', 'Aucune question pour cette espèce pour le moment. Soyez le premier à en poser une !');
        }
        
        return $this->render('forum/espece.html.twig', [
            'questions' => $questions,
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
