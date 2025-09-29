<?php

namespace App\Controller;

use App\Form\UserFilterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\AdminStats;


final class AdminController extends AbstractController
{

    #[Route('/admin', name: 'app_admin')]
    public function index(
        UserRepository $userRepository,
        Request $request,
        AdminStats $stats,
        PaginatorInterface $paginator
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Accès réservé aux administrateurs.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(UserFilterType::class);
        $form->handleRequest($request);

        // Query de base
        $qb = $userRepository->createQueryBuilder('u')->orderBy('u.pseudo', 'DESC');

        // Filtre recherche
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('pseudo')->getData();
            if ($pseudo) {
                $qb->andWhere('u.pseudo LIKE :q OR u.email LIKE :q')
                ->setParameter('q', '%'.$pseudo.'%');
            }
        }

        // Pagination (16 par page)
        $page = $request->query->getInt('page', 1);
        $users = $paginator->paginate($qb, $page, 15);

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'form'  => $form->createView(),
            'stats' => $stats->getStats(),
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
