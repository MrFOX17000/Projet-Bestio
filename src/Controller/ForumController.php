<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\EspeceRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(EspeceRepository $especeRepository): Response
    {
        $especes = $especeRepository->findEspecesWithQuestions();

        return $this->render('forum/index.html.twig', [
            'especes' => $especes,
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
    public function showQuestion(string $nom_espece, int $id, QuestionRepository $questionRepository,
     EspeceRepository $especeRepository, Request $request, CommentaireRepository $commentaireRepository,
     EntityManagerInterface $entityManager): Response
    {
        $espece = $especeRepository->findOneBy(['nomEspece' => $nom_espece]);

        if (!$espece) {
            $this->addFlash('warning', 'L\'espèce demandée n\'existe pas.');
            return $this->redirectToRoute('app_forum');
        }

        $question = $questionRepository->findOneBy(['id' => $id]);

        if (!$question) {
            $this->addFlash('warning', 'Aucune question trouvée.');
            return $this->redirectToRoute('app_forum');
        }

        $user = $this->getUser();


        $commentaires = $commentaireRepository->findBy(['question' => $question]);
        if (!$commentaires) {
            $this->addFlash('info', 'Aucune réponse pour cette question pour le moment. Soyez le premier à en poster une !');
        }

        $commentaire = new Commentaire();
        $commentaire->setQuestion($question);
        $commentaire->setAuthor($user);


        $formComm = $this->createForm(CommentaireType::class, $commentaire);
        $formComm->handleRequest($request);

        if ($formComm->isSubmitted() && $formComm->isValid()) {
            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'La réponse a bien été ajoutée');

           return $this->redirectToRoute('app_forum_question_show', [
            'nom_espece' => $nom_espece,
            'id' => $id,
        ]);

        }
        

        return $this->render('forum/question.html.twig', [
            'controller_name' => 'ForumController',
            'nom_espece' => $nom_espece,
            'question_id' => $id,
            "commentaires" => $commentaires,
            'formComm' => $formComm->createView()
        ]);
    }
}
