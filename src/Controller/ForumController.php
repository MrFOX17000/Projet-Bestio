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
    public function index(EspeceRepository $especeRepository, QuestionRepository $questionRepository): Response
    {
        $especes = $especeRepository->findEspecesWithQuestions();

        // Récupère tous les compteurs en une seule requête groupée
        $counts = $questionRepository->countQuestionsByEspece();

        return $this->render('forum/index.html.twig', [
            'especes' => $especes,
            'counts' => $counts,
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

    // Charge les questions avec leurs auteurs en une seule requête
    $questions = $questionRepository->findByEspeceWithAuthor($espece->getId());
        
        return $this->render('forum/espece.html.twig', [
            'questions' => $questions,
            'controller_name' => 'ForumController',
            'nom_espece' => $nom_espece,
            'nom' => $espece->getClasse()->getNom(),
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

    // Charge la question avec son auteur et son espèce
    $question = $questionRepository->findOneWithAuthorAndEspece($id);

        if (!$question) {
            $this->addFlash('warning', 'Aucune question trouvée.');
            return $this->redirectToRoute('app_forum');
        }

        $user = $this->getUser();

    // Charge les commentaires avec leurs auteurs en une seule requête
    $commentaires = $commentaireRepository->findByQuestionWithAuthor($id);

        $canComment = $user && $user !== $question->getAuthor();
        $formComm = null;

        if ($canComment) {
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
        }

        return $this->render('forum/question.html.twig', [
            'controller_name' => 'ForumController',
            'nom_espece' => $nom_espece,
            'question_id' => $id,
            'question' => $question,
            'commentaires' => $commentaires,
            'formComm' => $formComm ? $formComm->createView() : null,
            'canComment' => $canComment,
        ]);
    }
}
