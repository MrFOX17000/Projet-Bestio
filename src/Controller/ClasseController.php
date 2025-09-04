<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Classe;
use App\Form\ClasseType;
use App\Repository\ClasseRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ClasseController extends AbstractController
{
    #[Route('/add/classe', name: 'app_classe')]
    public function addClasse(Request $request, EntityManagerInterface $entityManager, ClasseRepository $classeRepository): Response
    {
        $classe = new Classe;
        $formClasse = $this->createForm(ClasseType::class, $classe);
        $formClasse->handleRequest($request);
        if ($formClasse->isSubmitted() && $formClasse->isValid()) {
            $image = $formClasse->get('image')->getData(); //On récupère le fichier uploadé depuis le formulaire
            if ($image) {
                $imageDirectory = $this->getParameter('images_directory'); // On récupère le chemin du dossier d'upload depuis le fichier services.yaml

                $newFilename = uniqid().'.'.$image->guessExtension(); //On renomme chaque fichier pour éviter les conflits de noms
                 try {
                        $image->move($imageDirectory, $newFilename); //On déplace le fichier dans le dossier Public/Uploads
                    } catch (FileException $e) {
                      // Si l'upload rencontre un problème on affiche un message d'erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());

                      //Et on redirige vers le formulaire
                    return $this->redirectToRoute('add_classe');
                    }
                    $classe->setImage('/uploads/' . $newFilename); // On stocke le chemin relatif de l'image dans la base de données
            }
            $entityManager->persist($classe);
            $entityManager->flush();
            $this->addFlash('success', 'Classe ajoutée avec succès !');
            return $this->redirectToRoute('app_classe');
        }

        $classes = $classeRepository->findAll();
        return $this->render('classe/index.html.twig', [
            'formClasse' => $formClasse->createView(),
            'classes' => $classes
        ]);
    }

    #[Route('/classe/{nom}', name: 'app_show_classe')]
    public function showClasse(ClasseRepository $classeRepository, string $nom): Response
    {
        $classe = $classeRepository->findOneBy(['nom' => $nom]);
        if(!$classe)
            {
                $this->addFlash('warning', 'La page ' . $nom . ' n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }
        return $this->render('classe/show.html.twig', [
            'classe' => $classe
        ]);
    }

    #[Route('/delete/classe/{id}', name: 'delete_classe')]
    public function deleteClasse(EntityManagerInterface $entityManager, ClasseRepository $classeRepository, int $id): Response
    {
        $classe = $classeRepository->find($id);
        if(!$classe)
            {
                $this->addFlash('error', 'Cette classe n\'existe pas.');
                return $this->redirectToRoute('app_classe');
            }

            $entityManager->remove($classe);
            $entityManager->flush();
            $this->addFlash('success', ' La classe a bien été supprimée');


            return $this->redirectToRoute('app_classe');
    }
}
