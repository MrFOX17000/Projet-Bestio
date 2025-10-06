<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Entity\Question;
use App\Entity\Commentaire;
use App\Entity\QuestionVote;
use App\Entity\CommentaireVote;
use App\Form\CommentaireType;
use App\Repository\EspeceRepository;
use App\Repository\QuestionRepository;
use App\Repository\CommentaireRepository;
use App\Repository\QuestionVoteRepository;
use App\Repository\CommentaireVoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ForumController extends AbstractController
{
    /////////////////////////////////////////////////////////////////////// LISTE DES ESPECES AYANT DES QUESTIONS
    #[Route('/forum', name: 'app_forum')]
    public function index(
        EspeceRepository $especeRepository,
        QuestionRepository $questionRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $data = $especeRepository->findEspecesWithQuestions();
        $especes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            6
        );
        $counts = $questionRepository->countQuestionsByEspece();

        return $this->render('forum/index.html.twig', [
            'especes' => $especes,
            'counts'  => $counts,
        ]);
    }

    /////////////////////////////////////////////////////////////////////// LISTE DES QUESTIONS POUR UNE ESPECE (tri)
    #[Route('/forum/{nom_espece}', name: 'app_forum_show')]
    public function show(
        string $nom_espece,
        QuestionRepository $questionRepository,
        EspeceRepository $especeRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $espece = $especeRepository->findOneBy(['nomEspece' => $nom_espece]);
        if (!$espece) {
            $this->addFlash('warning', 'Espèce inconnue.');
            return $this->redirectToRoute('app_forum');
        }

        $sort = $request->query->get('sort', 'top'); // top|recent

        // Méthode optimisée (si ajoutée) sinon fallback.
        if (method_exists($questionRepository, 'findForEspeceOrdered')) {
            $data = $questionRepository->findForEspeceOrdered($espece->getId(), $sort);
        } else {
            $data = $questionRepository->findByEspeceWithAuthor($espece->getId());
            if ($sort === 'top') {
                usort($data, fn($a, $b) => $b->getScore() <=> $a->getScore() ?: $b->getCreatedAt() <=> $a->getCreatedAt());
            } else {
                usort($data, fn($a, $b) => $b->getCreatedAt() <=> $a->getCreatedAt());
            }
        }

        $questions = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('forum/espece.html.twig', [
            'questions'   => $questions,
            'nom_espece'  => $nom_espece,
            'nom'         => $espece->getClasse()->getNom(),
            'sort'        => $sort,
        ]);
    }

    /////////////////////////////////////////////////////////////////////// DETAIL D'UNE QUESTION + COMMENTAIRES (tri)
    #[Route('/forum/{nom_espece}/question/{id}', name: 'app_forum_question_show')]
    public function showQuestion(
        string $nom_espece,
        int $id,
        QuestionRepository $questionRepository,
        EspeceRepository $especeRepository,
        CommentaireRepository $commentaireRepository,
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        $espece = $especeRepository->findOneBy(['nomEspece' => $nom_espece]);
        if (!$espece) {
            $this->addFlash('warning', 'Espèce inconnue.');
            return $this->redirectToRoute('app_forum');
        }

        $question = method_exists($questionRepository, 'findOneWithAuthorAndEspece')
            ? $questionRepository->findOneWithAuthorAndEspece($id)
            : $questionRepository->find($id);

        if (!$question) {
            $this->addFlash('warning', 'Question introuvable.');
            return $this->redirectToRoute('app_forum_show', ['nom_espece' => $nom_espece]);
        }

        $user = $this->getUser();
        $canComment = $user && $user !== $question->getAuthor() || $this->isGranted('ROLE_ADMIN');

        $sortComments = $request->query->get('sort', 'top');

        if (method_exists($commentaireRepository, 'findForQuestionOrdered')) {
            $commentData = $commentaireRepository->findForQuestionOrdered($id, $sortComments);
        } else {
            $commentData = $commentaireRepository->findByQuestionWithAuthor($id);
            if ($sortComments === 'top') {
                usort($commentData, fn($a, $b) => $b->getScore() <=> $a->getScore() ?: $b->getCreatedAtComm() <=> $a->getCreatedAtComm());
            } else {
                usort($commentData, fn($a, $b) => $b->getCreatedAtComm() <=> $a->getCreatedAtComm());
            }
        }

        $commentaires = $paginator->paginate(
            $commentData,
            $request->query->getInt('page', 1),
            12
        );

        // Formulaire commentaire
        $formComm = null;
        if ($canComment) {
            $commentaire = new Commentaire();
            $commentaire->setQuestion($question);
            $commentaire->setAuthor($user);

            $formComm = $this->createForm(CommentaireType::class, $commentaire);
            $formComm->handleRequest($request);

            if ($formComm->isSubmitted() && $formComm->isValid()) {
                $em->persist($commentaire);
                $em->flush();
                $this->addFlash('success', 'Réponse ajoutée.');
                return $this->redirectToRoute('app_forum_question_show', [
                    'nom_espece' => $nom_espece,
                    'id'         => $id,
                    'sort'       => $sortComments
                ]);
            }
        }

        return $this->render('forum/question.html.twig', [
            'question'      => $question,
            'commentaires'  => $commentaires,
            'nom_espece'    => $nom_espece,
            'formComm'      => $formComm ? $formComm->createView() : null,
            'canComment'    => $canComment,
            'sortComments'  => $sortComments,
        ]);
    }

    /////////////////////////////////////////////////////////////////////// VOTE QUESTION
    #[Route('/forum/question/{id}/vote/{value}', name: 'question_vote', methods: ['POST'])]
    public function voteQuestion(
        Question $question,
        int $value,
        QuestionVoteRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) return new JsonResponse(['error' => 'auth'], 401);
        if ($question->getAuthor() === $user) return new JsonResponse(['error' => 'owner'], 403);
        if (!in_array($value, [1, -1], true)) return new JsonResponse(['error' => 'bad'], 400);

        $vote = $repo->findOneBy(['question' => $question, 'user' => $user]);
        if (!$vote) {
            $vote = (new QuestionVote())->setQuestion($question)->setUser($user)->setValue($value);
            $em->persist($vote);
        } else {
            if ($vote->getValue() === $value) {
                if (method_exists($question, 'removeVote')) $question->removeVote($vote);
                $em->remove($vote);
                $vote = null;
            } else {
                $vote->setValue($value);
            }
        }
        $em->flush();

        $score = method_exists($repo, 'getScore')
            ? $repo->getScore($question->getId())
            : $question->getScore();

        return new JsonResponse([
            'score'    => $score,
            'userVote' => $vote?->getValue()
        ]);
    }

    /////////////////////////////////////////////////////////////////////// VOTE COMMENTAIRE
    #[Route('/forum/commentaire/{id}/vote/{value}', name: 'commentaire_vote', methods: ['POST'])]
    public function voteCommentaire(
        Commentaire $commentaire,
        int $value,
        CommentaireVoteRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) return new JsonResponse(['error' => 'auth'], 401);
        if ($commentaire->getAuthor() === $user) return new JsonResponse(['error' => 'owner'], 403);
        if (!in_array($value, [1, -1], true)) return new JsonResponse(['error' => 'bad'], 400);

        $vote = $repo->findOneBy(['commentaire' => $commentaire, 'user' => $user]);
        if (!$vote) {
            $vote = (new CommentaireVote())->setCommentaire($commentaire)->setUser($user)->setValue($value);
            $em->persist($vote);
        } else {
            if ($vote->getValue() === $value) {
                if (method_exists($commentaire, 'removeVote')) $commentaire->removeVote($vote);
                $em->remove($vote);
                $vote = null;
            } else {
                $vote->setValue($value);
            }
        }
        $em->flush();

        $score = method_exists($repo, 'getScore')
            ? $repo->getScore($commentaire->getId())
            : $commentaire->getScore();

        return new JsonResponse([
            'score'    => $score,
            'userVote' => $vote?->getValue()
        ]);
    }

    /////////////////////////////////////////////////////////////////////// SUPPRESSION QUESTION
    #[Route('/delete/question/{id}', name: 'delete_question')]
    public function deleteQuestion(
        int $id,
        QuestionRepository $questionRepository,
        CommentaireRepository $commRepository,
        EntityManagerInterface $em
    ): Response {
        $userLogin = $this->getUser();
        if (!$userLogin) throw $this->createAccessDeniedException();

        $question = $questionRepository->find($id);
        if (!$question) {
            $this->addFlash('warning', 'Question inconnue.');
            return $this->redirectToRoute('app_forum');
        }
        $nom_espece = $question->getEspece()->getNomEspece();

        if ($question->getAuthor() === $userLogin || $this->isGranted('ROLE_ADMIN')) {
            foreach ($commRepository->findBy(['question' => $question]) as $c) {
                $em->remove($c);
            }
            $em->remove($question);
            $em->flush();
            $this->addFlash('success', 'Question supprimée.');
        } else {
            $this->addFlash('warning', 'Action interdite.');
        }

        return $this->redirectToRoute('app_forum_show', ['nom_espece' => $nom_espece]);
    }

    /////////////////////////////////////////////////////////////////////// SUPPRESSION COMMENTAIRE
    #[Route('/delete/commentaire/{id}', name: 'delete_commentaire')]
    public function deleteCommentaire(
        int $id,
        CommentaireRepository $commRepository,
        EntityManagerInterface $em
    ): Response {
        $userLogin = $this->getUser();
        if (!$userLogin) throw $this->createAccessDeniedException();

        $commentaire = $commRepository->find($id);
        if (!$commentaire) {
            $this->addFlash('warning', 'Commentaire introuvable.');
            return $this->redirectToRoute('app_forum');
        }

        $question = $commentaire->getQuestion();
        $nom_espece = $question->getEspece()->getNomEspece();

        if ($commentaire->getAuthor() === $userLogin || $this->isGranted('ROLE_ADMIN')) {
            $em->remove($commentaire);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé.');
        } else {
            $this->addFlash('warning', 'Action interdite.');
        }

        return $this->redirectToRoute('app_forum_question_show', [
            'nom_espece' => $nom_espece,
            'id'         => $question->getId()
        ]);
    }

    /////////////////////////////////////////////////////////////////////// LOCK / UNLOCK
    #[Route('/question/lock/{id}', name: 'lock_question')]
    public function lock(
        int $id,
        QuestionRepository $questionRepository,
        EntityManagerInterface $em
    ): Response {
        $userLogin = $this->getUser();
        if (!$userLogin) throw $this->createAccessDeniedException();

        $question = $questionRepository->find($id);
        if (!$question) {
            $this->addFlash('warning', 'Question introuvable.');
            return $this->redirectToRoute('app_forum');
        }

        if ($question->getAuthor() === $userLogin || $this->isGranted('ROLE_ADMIN')) {
            $question->setLocked(true);
            $em->flush();
        } else {
            $this->addFlash('warning', 'Action interdite.');
        }

        return $this->redirectToRoute('app_forum_question_show', [
            'nom_espece' => $question->getEspece()->getNomEspece(),
            'id'         => $question->getId()
        ]);
    }

    #[Route('/question/unlock/{id}', name: 'unlock_question')]
    public function unlock(
        int $id,
        QuestionRepository $questionRepository,
        EntityManagerInterface $em
    ): Response {
        $userLogin = $this->getUser();
        if (!$userLogin) throw $this->createAccessDeniedException();

        $question = $questionRepository->find($id);
        if (!$question) {
            $this->addFlash('warning', 'Question introuvable.');
            return $this->redirectToRoute('app_forum');
        }

        if ($question->getAuthor() === $userLogin || $this->isGranted('ROLE_ADMIN')) {
            $question->setLocked(false);
            $em->flush();
        } else {
            $this->addFlash('warning', 'Action interdite.');
        }

        return $this->redirectToRoute('app_forum_question_show', [
            'nom_espece' => $question->getEspece()->getNomEspece(),
            'id'         => $question->getId()
        ]);
    }
}
