<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

final class UserController extends AbstractController
{
    #[Route('/profil/{id}', name: 'user')]
    #[IsGranted('ROLE_USER')]
    public function userProfile(User $user): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser === $user) {
            return $this->redirectToRoute('current_user');
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/profil', name: 'current_user')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function currentUserprofile(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Création du formulaire à partir de ton UserType
        $userForm = $this->createForm(UserType::class, $user);

        // On enlève l'ancien champ password (car on ne veut pas le modifier directement)
        $userForm->remove('password');

        // On ajoute un champ "newPassword" non mappé
        $userForm->add('newPassword', PasswordType::class, [
            'required' => false,
            'mapped'   => false, // très important pour éviter que Doctrine essaie de le persister
            'label'    => 'Nouveau mot de passe :',
            'attr'     => [
                'placeholder' => 'Votre nouveau mot de passe',
            ],
        ]);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $newPassword = $userForm->get('newPassword')->getData();

            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword); // <-- on met à jour le vrai champ persisté
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('current_user'); // pour éviter la resoumission du form
        }

        return $this->render('user/index.html.twig', [
            'form' => $userForm->createView(),
        ]);
    }
}
