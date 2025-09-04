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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;

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
        return $this->render('user/show.html.twig', [
            'user' => $user,
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
        $userForm->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['constraints' => [
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/',
                            'message' => 'Le mot de passe doit contenir au minimum une majuscule, une minuscule, un chiffre et 12 caractères dont un caractère spécial',
                        ]),
                    ],
                    'label' => 'Mot de passe :',
                    'attr' => ['placeholder' => 'Nouveau mot de passe']
                ],
                'second_options' => ['label' => 'Confirmer le mot de passe :',
                    'attr' => ['placeholder' => 'Confirmer le nouveau mot de passe']
                ],
                'mapped' => false,
           
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
