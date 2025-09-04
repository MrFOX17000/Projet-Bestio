<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Form\EspeceType;
use App\Repository\EspeceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class EspeceController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EspeceRepository $especeRepository): Response
    {
        $especes = $especeRepository->findAll();
        return $this->render('espece/index.html.twig', [
            'especes' => $especes
        ]);
    }

    #[Route('/add/espece', name: 'add_espece')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $espece = new Espece();
        $form = $this->createForm(EspeceType::class, $espece);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData(); //On récupère le fichier uploadé depuis le formulaire
            if ($image) {
                $imageDirectory = $this->getParameter('images_directory'); // On récupère le chemin du dossier d'upload depuis le fichier services.yaml

                $newFilename = uniqid().'.'.$image->guessExtension(); //On renomme chaque fichier pour éviter les conflits de noms
                 try {
                        $image->move($imageDirectory, $newFilename); //On déplace le fichier dans le dossier Public/Uploads
                    } catch (FileException $e) {
                      // Si l'upload rencontre un problème on affiche un message d'erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                            
                      //Et on redirige vers le formulaire
                    return $this->redirectToRoute('add_espece');
                    }
                    $espece->setImage('/uploads/' . $newFilename); // On stocke le chemin relatif de l'image dans la base de données
        }

                $entityManager->persist($espece);
                $entityManager->flush();
                $this->addFlash('success', 'Espèce ajoutée avec succès !');
                return $this->redirectToRoute('add_espece');

    }

        return $this->render('espece/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/espece/{id}', name: 'detail_espece')]
    public function detail(Request $request, EspeceRepository $especeRepository, int $id): Response
    {
        $espece = $especeRepository->find($id);
        if(!$espece)
            {
                $this->addFlash('message', 'Cette espèce n\'existe pas.');
                return $this->redirectToRoute('app_espece');
            }
        return $this->render('espece/details.html.twig', [
            'espece' => $espece
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_espece')]
    public function delete(Request $request, EntityManagerInterface $entityManager, EspeceRepository $especeRepository, int $id): Response
    {
        $espece = $especeRepository->find($id);
        if(!$espece)
            {
                $this->addFlash('error', 'Cette espèce n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }

            $entityManager->remove($espece);
            $entityManager->flush();
            $this->addFlash('success', ' L\'espèce a bien été supprimée');


            return $this->redirectToRoute('app_home');
    }


}
