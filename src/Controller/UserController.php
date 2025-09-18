<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuestionRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function currentUserprofile(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, QuestionRepository $questionRepository, CommentaireRepository $commentaireRepository): Response
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
                'required' => false,
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

             $image = $userForm->get('photo')->getData(); //On récupère le fichier uploadé depuis le formulaire
            if ($image) {
                $avatarDirectory = $this->getParameter('avatar_directory'); // On récupère le chemin du dossier d'upload depuis le fichier services.yaml

                $newFilename = uniqid().'.'.$image->guessExtension(); //On renomme chaque fichier pour éviter les conflits de noms
                 try {
                        $image->move($avatarDirectory, $newFilename); //On déplace le fichier dans le dossier Public/Uploads
                    } catch (FileException $e) {
                      // Si l'upload rencontre un problème on affiche un message d'erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                            
                      //Et on redirige vers le formulaire
                    return $this->redirectToRoute('current_user');
                    }
                    $user->setPhoto('/uploads/avatar/' . $newFilename); // On stocke le chemin relatif de l'image dans la base de données
        }
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

        $myQuestions = $questionRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);
        $myComments = $commentaireRepository->findBy(['author' => $user], ['createdAtComm' => 'DESC']);

        return $this->render('user/index.html.twig', [
            'form' => $userForm->createView(),
            'myQuestions' => $myQuestions,
            'myComments' => $myComments,
            'isVerified' => $user->isVerified(),
        ]);
    }
}
