<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\EspeceRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ForumController extends AbstractController
{
    
    /////////////////////////////////////////////////////////////////////////////Affichage des espèces sur le forum (avec au moins une question)///////////////////////////////////////////////////////////////////////////

    #[Route('/forum', name: 'app_forum')]
    public function index(EspeceRepository $especeRepository, QuestionRepository $questionRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $especeRepository->findEspecesWithQuestions();

        //Pagination des animaux à l'aide de KNB Paginator
                $especes = $paginatorInterface->paginate
                (
                $data,
                $request->query->getInt('page', 1),
                8 // Nombre d'éléments par page
                );

        // Récupère tous les compteurs en une seule requête groupée
        $counts = $questionRepository->countQuestionsByEspece();

        return $this->render('forum/index.html.twig', [
            'especes' => $especes,
            'counts' => $counts,
        ]);
    }

    
    /////////////////////////////////////////////////////////////////////////////Affichage des questions par rapport à une espèce///////////////////////////////////////////////////////////////////////////

    #[Route('/forum/{nom_espece}', name: 'app_forum_show')]
    public function show(string $nom_espece, QuestionRepository $questionRepository, EspeceRepository $especeRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $espece = $especeRepository->findOneBy(['nomEspece' => $nom_espece]);

        if (!$espece) {
            $this->addFlash('warning', 'L\'espèce demandée n\'existe pas.');
            return $this->redirectToRoute('app_forum');
        }

    // Charge les questions avec leurs auteurs en une seule requête
    $data = $questionRepository->findByEspeceWithAuthor($espece->getId());

     //Pagination des questions à l'aide de KNB Paginator
                $questions = $paginatorInterface->paginate
                (
                $data,
                $request->query->getInt('page', 1),
                8 // Nombre d'éléments par page
                );
        
        return $this->render('forum/espece.html.twig', [
            'questions' => $questions,
            'controller_name' => 'ForumController',
            'nom_espece' => $nom_espece,
            'nom' => $espece->getClasse()->getNom(),
        ]);
    }

    
    /////////////////////////////////////////////////////////////////////////////Affichage des réponses à une question///////////////////////////////////////////////////////////////////////////

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

        $canComment = $user && $user !== $question->getAuthor() || $this->isGranted('ROLE_ADMIN');
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
    
    /////////////////////////////////////////////////////////////////////////////Suppression d'une question///////////////////////////////////////////////////////////////////////////

   #[Route('/delete/question/{id}', name: 'delete_question')]
        public function deleteQuestion(
            int $id,
            QuestionRepository $questionRepository, CommentaireRepository $commRepository, EntityManagerInterface $entityManager ): Response 
    {
            $userLogin = $this->getUser();

            if (!$userLogin) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
            }

            $roles = $userLogin->getRoles();

            $question = $questionRepository->find($id);
            $nom_espece = $question->getEspece()->getNomEspece();


            if (!$question) {
                $this->addFlash('warning', 'Cette question n\'existe pas.');
                return $this->redirectToRoute('app_forum_show', ['nom_espece' => $nom_espece]);
            }

            if ($question->getAuthor() === $userLogin || in_array('ROLE_ADMIN', $roles)) {
                $commentaires = $commRepository->findBy(['question' => $question]);

                foreach ($commentaires as $commentaire) {
                    $entityManager->remove($commentaire);
                }

                $entityManager->remove($question);
                $entityManager->flush();

                $this->addFlash('success', 'La question a bien été supprimée.');
            } else {
                $this->addFlash('success', 'Vous ne pouvez pas supprimer cette question.');
            }

           return $this->redirectToRoute('app_forum_show', ['nom_espece' => $nom_espece]);
    }


    /////////////////////////////////////////////////////////////////////////////Suppression d'un commentaire///////////////////////////////////////////////////////////////////////////

   #[Route('/delete/commentaire/{id}', name: 'delete_commentaire')]
    public function deleteCommentaire(
        int $id, CommentaireRepository $commRepository, EntityManagerInterface $entityManager ): Response {
        $userLogin = $this->getUser();

        if (!$userLogin) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $roles = $userLogin->getRoles();
        $commentaire = $commRepository->find($id);

        if (!$commentaire) {
            $this->addFlash('warning', 'Ce commentaire n\'existe pas.');
            return $this->redirectToRoute('app_forum');
        }

        $question = $commentaire->getQuestion();
        $nom_espece = $question->getEspece()->getNomEspece();
        $question_id = $question->getId();

        if ($commentaire->getAuthor() === $userLogin || in_array('ROLE_ADMIN', $roles)) {
            $entityManager->remove($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a bien été supprimé.');
        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer ce commentaire.');
        }

        return $this->redirectToRoute('app_forum_question_show', [
            'nom_espece' => $nom_espece,
            'id' => $question_id,
        ]);
    }

    
    /////////////////////////////////////////////////////////////////////////////Verrouillage d'une question///////////////////////////////////////////////////////////////////////////
    #[Route('/question/lock/{id}', name: 'lock_question')]
    public function lock($id , QuestionRepository $questionRepository, EntityManagerInterface $entityManager): Response
    {
        $userLogin = $this->getUser();

        if(!$userLogin) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $roles = $userLogin->getRoles();
        $question = $questionRepository->find($id);

        if (!$question) {
            $this->addFlash('warning', 'Cette question n\'existe pas');
            return $this->redirectToRoute('app_forum'); 
        }


        if ($question->getAuthor() === $userLogin || in_array("ROLE_ADMIN", $roles)) {
            $question->setLocked(true);
            $entityManager->persist($question);
            $entityManager->flush();

           return $this->redirectToRoute('app_forum_question_show', [
            'nom_espece' => $question->getEspece()->getNomEspece(), 
            'id' => $question->getId(),
        ]);

        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas verrouiller ce topic.');
           return $this->redirectToRoute('app_forum_question_show', [
                'nom_espece' => $question->getEspece()->getNomEspece(), 
                'id' => $question->getId(),
            ]);
        }
    }

     /////////////////////////////////////////////////////////////////////////////Déverrouillage d'une question///////////////////////////////////////////////////////////////////////////

 #[Route('/question/unlock/{id}', name: 'unlock_question')]
    public function unlock($id, QuestionRepository $questionRepository, EntityManagerInterface $entityManager): Response
    {
        $userLogin = $this->getUser();

        if (!$userLogin) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $roles = $userLogin->getRoles();

        $question = $questionRepository->find($id);

       
        if (!$question) {
            $this->addFlash('warning', 'Ce topic n\'existe pas.');
            return $this->redirectToRoute('app_forum'); 
        }

        if ($question->getAuthor() === $userLogin || in_array("ROLE_ADMIN", $roles)) {
            $question->setLocked(false);
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_question_show', [
            'nom_espece' => $question->getEspece()->getNomEspece(),
            'id' => $question->getId(),
            ]);
        } else {

            $this->addFlash('warning', 'Vous ne pouvez pas déverrouiller cette question.');
            return $this->redirectToRoute('app_forum_question_show', [
                'nom_espece' => $question->getEspece()->getNomEspece(),
                'id' => $question->getId(),
            ]);
        }
    }


}
