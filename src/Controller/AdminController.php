<?php

namespace App\Controller;

use App\Form\UserFilterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AdminController extends AbstractController
{

     #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
         if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('warning', 'Accès réservé aux administrateurs.');
        return $this->redirectToRoute('app_home');
        }

        $userLogin = $this->getUser();
        
        $form = $this->createForm(UserFilterType::class);

        $form->handleRequest($request);

        $users = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $pseudo = $filters['pseudo'];

            $users = $userRepository->findUserPseudo($pseudo);
        } else {
            $users = $userRepository->findBy([], ["pseudo" => "DESC"]);
        }

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
        ]);
    }

    
        #[Route('/admin/ban/{id}', name: 'ban_admin')]
        public function ban(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
        {
            $userLogin = $this->getUser(); 

            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('warning', 'Accès réservé aux administrateurs.');
                return $this->redirectToRoute('app_home');
            }

            $roles = $userLogin->getRoles();

            $user = $userRepository->find($id);

            if (!$user) {
                $this->addFlash('warning', 'Utilisateur introuvable.');
                return $this->redirectToRoute('app_admin');
            }

            if (in_array("ROLE_ADMIN", $roles)) {
                $user->setBanned(true);
                $entityManager->flush();

                $this->addFlash('success', 'L\'utilisateur a bien été banni.');
            }

            return $this->redirectToRoute('app_admin');
        }


    #[Route('/admin/unban/{id}', name: 'unban_admin')]
    public function unban(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
         if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('warning', 'Accès réservé aux administrateurs.');
        return $this->redirectToRoute('app_home');
        }

        $userLogin = $this->getUser();
        

        $roles = $userLogin->getRoles();
        
        $user = $userRepository->find($id);

        if(!$user) {
            $this->addFlash('warning', 'Utilisateur introuvable');
            return $this->redirectToRoute('app_admin');
        }

        if (in_array("ROLE_ADMIN", $roles)) {
        $user->setBanned(false);
        $entityManager->flush();

        $this->addFlash('warning', 'L\'utilisateur à bien été débanni');
        return $this->redirectToRoute('app_admin');
        } else {
            $this->addFlash('warning', 'Vous n\'avez pas l\'autorisation d\'effectuer cette action.');
            return $this->redirectToRoute('app_cat');
        }

    }

    
}
