<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Question;
use App\Form\ClasseType;
use App\Form\QuestionType;
use App\Form\ClasseNameType;
use App\Repository\ClasseRepository;
use App\Repository\EspeceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\ClasseImage;

final class ClasseController extends AbstractController
{

    #[Route('/classe', name: 'app_classe')]
    public function index(EspeceRepository $especeRepository, ClasseRepository $classeRepository, Request $request): Response
    {
        $especes = $especeRepository->findAll();
        $classes = $classeRepository->findAllWithCategorie(); // Utilise la méthode optimisée

        $form = $this->createForm(ClasseNameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();
            $classes = $classeRepository->searchByClasses($search);
        } else {
            $classes = $classeRepository->findAllWithCategorie();
        }

        // Grouper les classes par catégorie
        $classesParCategorie = [];
        foreach ($classes as $classe) {
            $categorie = $classe->getAppartenir()?->getNomCategorisation() ?? 'Autres';
            $classesParCategorie[$categorie][] = $classe;
        }

        return $this->render('classe/index.html.twig', [
            'especes' => $especes,
            'classesParCategorie' => $classesParCategorie,
             'form' => $form->createView(),
        ]);
    }

    #[Route('/add/classe', name: 'app_add_classe')]
    public function addClasse(Request $request, EntityManagerInterface $entityManager, ClasseRepository $classeRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Accès réservé aux administrateurs.');
        return $this->redirectToRoute('app_home');
        }

        $classe = new Classe;
        $formClasse = $this->createForm(ClasseType::class, $classe);
        $formClasse->handleRequest($request);
        if ($formClasse->isSubmitted() && $formClasse->isValid()) {
            $image = $formClasse->get('image')->getData(); //On récupère le fichier uploadé depuis le formulaire
            if ($image) {
                $imageDirectory = $this->getParameter('images_directory');
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move($imageDirectory, $newFilename);
                    $classe->setImage('/uploads/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_add_classe');
                }

                // Synchronise l'image principale -> ClasseImage (position 0)
                $webPath = '/uploads/' . $newFilename;
                $primary = null;
                foreach ($classe->getClasseImages() as $img) {
                    if ($img->getPosition() === 0) { $primary = $img; break; }
                }
                if (!$primary) {
                    $primary = new ClasseImage();
                    $primary->setPosition(0);
                    $classe->addClasseImage($primary);
                }
                $primary->setPath($webPath);

                // Supprime doublon exact éventuel dans la collection
                foreach ($classe->getClasseImages() as $img) {
                    if ($img !== $primary && $img->getPath() === $webPath) {
                        $classe->removeClasseImage($img);
                    }
                }
            }

            // Nouvelles images multiples
            $files = $formClasse->get('newImages')->getData();
            if ($files) {
                // chemins déjà existants (éviter doublons)
                $existing = [];
                foreach ($classe->getClasseImages() as $imgObj) { $existing[] = $imgObj->getPath(); }
                if ($classe->getImage() && !in_array($classe->getImage(), $existing, true)) { $existing[] = $classe->getImage(); }

                $maxTotal = 3; // principale + 2
                $currentCount = count($existing);
                $remainingSlots = max(0, $maxTotal - $currentCount);

                // prochaine position disponible
                $maxPos = -1;
                foreach ($classe->getClasseImages() as $img) { $maxPos = max($maxPos, $img->getPosition()); }
                $nextPos = $maxPos + 1;

                foreach (array_slice($files, 0, $remainingSlots) as $file) {
                    if (!$file instanceof UploadedFile) continue;
                    $name = uniqid().'.'.$file->guessExtension();
                    try {
                        $file->move($this->getParameter('images_directory'), $name);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur upload image multiple : '.$e->getMessage());
                        break;
                    }
                    $webPath = '/uploads/'.$name;
                    if (in_array($webPath, $existing, true)) continue; // évite doublon exact

                    $ci = new ClasseImage();
                    $ci->setPath($webPath)->setPosition($nextPos++);
                    $classe->addClasseImage($ci);
                    $existing[] = $webPath;
                }
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
    public function showEspece(ClasseRepository $classeRepository, string $nom, EspeceRepository $especeRepository, string $nom_espece, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $classe = $classeRepository->findOneBy(['nom' => $nom]);
        $espece = $especeRepository->findOneBy(['nomEspece' => $nom_espece]);
        if(!$classe || !$espece)
            {
                $this->addFlash('warning', 'La page n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }

        $question = new Question();
        $question->setEspece($espece);
        $question->setAuthor($user);


        $formQuestion = $this->createForm(QuestionType::class, $question);
        $formQuestion->handleRequest($request);

        if ($formQuestion->isSubmitted() && $formQuestion->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            $this->addFlash('success', 'La question a bien été ajoutée');

            return $this->redirectToRoute('app_forum_show', ['nom_espece' => $nom_espece]);
        }
        
        
        return $this->render('espece/details.html.twig', [
            'classe' => $classe,
            'nom_espece' => $nom_espece,
            'espece' => $espece,
            'formQuestion' => $formQuestion->createView(),
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
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès réservé aux administrateurs.');
            return $this->redirectToRoute('app_home');
            }

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

            // Mémorise l’ancienne principale (pour suppression éventuelle)
            $oldPrimaryPath = $classe->getImage();

            // 1) Image principale (si changée)
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

                // Synchronise la principale -> ClasseImage (position 0)
                $webPath = '/uploads/' . $newFilename;
                $primary = null;
                foreach ($classe->getClasseImages() as $img) {
                    if ($img->getPosition() === 0) { $primary = $img; break; }
                }
                if (!$primary) {
                    $primary = new ClasseImage();
                    $primary->setPosition(0);
                    $classe->addClasseImage($primary);
                }
                $primary->setPath($webPath);

                // Anti-doublon exact
                foreach ($classe->getClasseImages() as $img) {
                    if ($img !== $primary && $img->getPath() === $webPath) {
                        $classe->removeClasseImage($img);
                    }
                }
            }

            // 2) Images secondaires: si des fichiers sont fournis, on REMPLACE pos 1 et 2
            $files = $formClasse->get('newImages')->getData();
            if ($files) {
                // a) supprimer les anciennes secondaires (pos >= 1)
                $toDeletePaths = [];
                foreach ($classe->getClasseImages() as $img) {
                    if ($img->getPosition() >= 1) {
                        $toDeletePaths[] = $img->getPath();
                        $classe->removeClasseImage($img); // orphanRemoval => delete DB au flush
                    }
                }

                // b) ajouter les nouvelles (max 2) aux positions 1 et 2
                $pos = 1;
                foreach (array_slice($files, 0, 2) as $file) {
                    if (!$file instanceof UploadedFile) continue;
                    $name = uniqid() . '.' . $file->guessExtension();
                    try {
                        $file->move($this->getParameter('images_directory'), $name);
                    } catch (\Throwable $e) {
                        $this->addFlash('error', 'Erreur upload image multiple : ' . $e->getMessage());
                        break;
                    }
                    $ci = new ClasseImage();
                    $ci->setPath('/uploads/' . $name)->setPosition($pos++);
                    $classe->addClasseImage($ci);
                }

                // c) suppression physique des anciennes secondaires remplacées (si non réutilisées)
                $usedPaths = array_map(fn($ci) => $ci->getPath(), $classe->getClasseImages()->toArray());
                foreach ($toDeletePaths as $p) {
                    if (!in_array($p, $usedPaths, true)) {
                        $this->deleteWebFile($p);
                    }
                }
            }

            // 3) si la principale a changé, supprimer l’ancienne du disque si non utilisée
            if ($oldPrimaryPath && $oldPrimaryPath !== $classe->getImage()) {
                $usedPaths = array_map(fn($ci) => $ci->getPath(), $classe->getClasseImages()->toArray());
                if (!in_array($oldPrimaryPath, $usedPaths, true)) {
                    $this->deleteWebFile($oldPrimaryPath);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Classe modifiée avec succès !');

            return $this->redirectToRoute('app_show_classe', [
                'nom' => $classe->getNom()
            ]);
        }

        return $this->render('classe/edit.html.twig', [
            'formClasse' => $formClasse->createView(),
            'classe' => $classe
        ]);
    }

    // Helper pour supprimer un fichier du dossier uploads
    private function deleteWebFile(?string $webPath): void
    {
        if (!$webPath || !str_starts_with($webPath, '/uploads/')) return;
        $abs = $this->getParameter('kernel.project_dir') . '/public' . $webPath;
        if (is_file($abs)) { @unlink($abs); }
    }

    #[Route('/delete/classe/{id}', name: 'delete_classe')]
    public function deleteClasse(EntityManagerInterface $entityManager, ClasseRepository $classeRepository, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Accès réservé aux administrateurs.');
        return $this->redirectToRoute('app_home');
        }
        
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
