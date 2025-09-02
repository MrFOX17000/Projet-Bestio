<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Form\EspeceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class EspeceController extends AbstractController
{
    #[Route('/espece', name: 'app_espece')]
    public function index(): Response
    {
        return $this->render('espece/index.html.twig', [
            'controller_name' => 'EspeceController',
        ]);
    }

     #[Route('/add/espece', name: 'add_espece')]
    public function add(Request $request): Response
    {
        $espece = new Espece();
        $form = $this->createForm(EspeceType::class, $espece);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();
                 try {
                        $image->move($imageDirectory, $newFilename);
                    } catch (FileException $e) {
                      // Si l'upload rencontre un problème on affiche un message d'erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                            
                      //Et on redirige vers le formulaire
                    return $this->redirectToRoute('add_espece');
                    }
                    $espece->setImage('/uploads' . $newFilename);
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
}
