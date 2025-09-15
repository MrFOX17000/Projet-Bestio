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
use App\Repository\EspeceRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ClasseController extends AbstractController
{

    #[Route('/classe', name: 'app_classe')]
    public function index(EspeceRepository $especeRepository, ClasseRepository $classeRepository): Response
    {
        $especes = $especeRepository->findAll();
        $classes = $classeRepository->findAllWithCategorie(); // Utilise la méthode optimisée

        // Grouper les classes par catégorie
        $classesParCategorie = [];
        foreach ($classes as $classe) {
            $categorie = $classe->getAppartenir()?->getNomCategorisation() ?? 'Autres';
            $classesParCategorie[$categorie][] = $classe;
        }

        return $this->render('classe/index.html.twig', [
            'especes' => $especes,
            'classesParCategorie' => $classesParCategorie,
        ]);
    }

    #[Route('/add/classe', name: 'app_add_classe')]
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
                    return $this->redirectToRoute('app_add_classe');
                    }
                    $classe->setImage('/uploads/' . $newFilename); // On stocke le chemin relatif de l'image dans la base de données
            }
            $entityManager->persist($classe);
            $entityManager->flush();
            $this->addFlash('success', 'Classe ajoutée avec succès !');
            return $this->redirectToRoute('app_add_classe');
        }

        $classes = $classeRepository->findAll();
        return $this->render('classe/add.html.twig', [
            'formClasse' => $formClasse->createView(),
            'classes' => $classes
        ]);
    }

    #[Route('/classe/{nom}', name: 'app_show_classe')]
    public function showClasse(ClasseRepository $classeRepository, string $nom): Response
    {
        $classe = $classeRepository->findOneWithRelations($nom);        
        if(!$classe)
            {
                $this->addFlash('warning', 'La page ' . $nom . ' n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }
        return $this->render('classe/show.html.twig', [
            'classe' => $classe
        ]);
    }

    #[Route('classe/{nom}/espece/', name: 'app_espece')]
    public function espece(EspeceRepository $especeRepository, ClasseRepository $classeRepository, string $nom): Response
    {
        $classe = $classeRepository->findOneBy(['nom' => $nom]);
        if (!$classe) {
            $this->addFlash('warning', 'La classe demandée n\'existe pas.');
            return $this->redirectToRoute('app_classe');
        }

        // On suppose que tu as une relation OneToMany entre Classe et Espece
        $especes = $classe->getDependre();

        return $this->render('espece/index.html.twig', [
            'classe' => $classe,
            'especes' => $especes,
        ]);
    }

    #[Route('/classe/{nom}/espece/{nom_espece}', name: 'app_show_espece')]
    public function showEspece(ClasseRepository $classeRepository, string $nom, EspeceRepository $especeRepository, string $nom_espece): Response
    {
        $classe = $classeRepository->findOneBy(['nom' => $nom]);
        $espece = $especeRepository->findOneBy(['nomEspece' => $nom_espece]);
        if(!$classe || !$espece)
            {
                $this->addFlash('warning', 'La page n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }
        return $this->render('espece/details.html.twig', [
            'classe' => $classe,
            'nom_espece' => $nom_espece,
            'espece' => $espece,
        ]);
    }

    #[Route('/edit/classe/{id}', name: 'edit_classe')]
public function editClasse(
    int $id,
    Request $request,
    EntityManagerInterface $entityManager,
    ClasseRepository $classeRepository
): Response
{
    // On récupère la classe existante
    $classe = $classeRepository->find($id);

    if (!$classe) {
        $this->addFlash('error', 'Cette classe n\'existe pas.');
        return $this->redirectToRoute('app_classe');
    }

    // On crée le formulaire en injectant l'entité existante
    $formClasse = $this->createForm(ClasseType::class, $classe, [
        'method' => 'POST',
    ]);
    $formClasse->handleRequest($request);

    if ($formClasse->isSubmitted() && $formClasse->isValid()) {
        // Gestion de l'image si un nouveau fichier est uploadé
        $image = $formClasse->get('image')->getData();
        if ($image) {
            $imageDirectory = $this->getParameter('images_directory');
            $newFilename = uniqid() . '.' . $image->guessExtension();

            try {
                $image->move($imageDirectory, $newFilename);
                $classe->setImage('/uploads/' . $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                return $this->redirectToRoute('edit_classe', ['id' => $id]);
            }
        }
        // Si aucune image n'est uploadée, on garde l'image existante

        $entityManager->flush();
        $this->addFlash('success', 'Classe modifiée avec succès !');
        return $this->redirectToRoute('app_classe');
    }

    return $this->render('classe/edit.html.twig', [
        'formClasse' => $formClasse->createView(),
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
