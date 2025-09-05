<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Form\EspeceType;
use App\Repository\ClasseRepository;
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
    public function index(EspeceRepository $especeRepository, ClasseRepository $classeRepository): Response
    {
        $especes = $especeRepository->findAll();
        $classes = $classeRepository->findAll();

        // Grouper les classes par catégorie
        $classesParCategorie = [];
        foreach ($classes as $classe) {
            $categorie = $classe->getAppartenir()?->getNomCategorisation() ?? 'Autres';
            $classesParCategorie[$categorie][] = $classe;
        }

        return $this->render('espece/home.html.twig', [
            'especes' => $especes,
            'classesParCategorie' => $classesParCategorie,
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

    #[Route('/edit/espece/{id}', name: 'edit_espece')]
public function editEspece(
    int $id,
    Request $request,
    EntityManagerInterface $entityManager,
    EspeceRepository $especeRepository
): Response
{
    // On récupère l'espece existante
    $espece = $especeRepository->find($id);

    if (!$espece) {
        $this->addFlash('error', 'Cette espèce n\'existe pas.');
        return $this->redirectToRoute('app_classe');
    }

    // On crée le formulaire en injectant l'entité existante
    $formEspece = $this->createForm(EspeceType::class, $espece, [
        'method' => 'POST',
    ]);
    $formEspece->handleRequest($request);

    if ($formEspece->isSubmitted() && $formEspece->isValid()) {
        // Gestion de l'image si un nouveau fichier est uploadé
        $image = $formEspece->get('image')->getData();
        if ($image) {
            $imageDirectory = $this->getParameter('images_directory');
            $newFilename = uniqid() . '.' . $image->guessExtension();

            try {
                $image->move($imageDirectory, $newFilename);
                $espece->setImage('/uploads/' . $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                return $this->redirectToRoute('edit_espece', ['id' => $id]);
            }
        }
        // Si aucune image n'est uploadée, on garde l'image existante

        $entityManager->flush();
        $this->addFlash('success', 'Espèce modifiée avec succès !');
        return $this->redirectToRoute('edit_espece', ['id' => $id]);
    }

    return $this->render('espece/edit.html.twig', [
        'formEspece' => $formEspece->createView(),
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
