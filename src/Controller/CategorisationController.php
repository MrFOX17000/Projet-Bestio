<?php

namespace App\Controller;

use App\Form\SearchClassType;
use App\Entity\Categorisation;
use App\Form\CategorisationType;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategorisationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategorisationController extends AbstractController
{
    #[Route('/categorie/{categorie}', name: 'app_categorie_show')]
    public function showCategorie(
        CategorisationRepository $categorisationRepository,
        ClasseRepository $classeRepository,
        string $categorie,
        PaginatorInterface $paginatorInterface,
        Request $request
    ): Response {
        $categorieEntity = $categorisationRepository->findOneBy(['nomCategorisation' => $categorie]);
        if (!$categorieEntity) {
            $this->addFlash('error', 'Cette catégorie n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }

        // Création du formulaire
        $form = $this->createForm(SearchClassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $search = $form->get('search')->getData();
        
        if ($search) {
            $data = $classeRepository->searchByName($search, $categorieEntity->getId());
        } else {
            $data = $categorieEntity->getClasses();
        }
        } else {
            $data = $categorieEntity->getClasses();
        }

        // Pagination
        $classes = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('categorisation/show.html.twig', [
            'categorie' => $categorieEntity,
            'classes' => $classes,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add/categorisation', name: 'add_categorisation')]
    public function addCategorisation(Request $request, EntityManagerInterface $entityManager, CategorisationRepository $categoRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Accès réservé aux administrateurs.');
        return $this->redirectToRoute('app_home');
        }

        $categorisation = new Categorisation;
        $formCategorisation = $this->createForm(CategorisationType::class, $categorisation);
        $formCategorisation->handleRequest($request);
        if ($formCategorisation->isSubmitted() && $formCategorisation->isValid()) {
            $entityManager->persist($categorisation);
            $entityManager->flush();
            $this->addFlash('success', 'Catégorisation ajoutée avec succès !');
            return $this->redirectToRoute('app_categorisation');
        }

        $categories = $categoRepository->findAll();
        return $this->render('categorisation/add.html.twig', [
            'formCategorisation' => $formCategorisation->createView(),
            'categories' => $categories
        ]);
    }

    #[Route('/edit/categorie/{id}', name: 'edit_categorie')]
    public function editCategorie(Request $request, EntityManagerInterface $entityManager, CategorisationRepository $categorisationRepository, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Accès réservé aux administrateurs.');
        return $this->redirectToRoute('app_home');
        }

        $categorie = $categorisationRepository->find($id);
        if(!$categorie)
            {
                $this->addFlash('error', 'Cette catégorie n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }
        $formCategorie = $this->createForm(CategorisationType::class, $categorie);
        $formCategorie->handleRequest($request);
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('categorisation/edit.html.twig', [
            'formCategorie' => $formCategorie->createView(),
            'categorie' => $categorie
        ]);
    }

    #[Route('/delete/categorie/{id}', name: 'delete_categorie')]
    public function deleteCategorie(Request $request, EntityManagerInterface $entityManager, CategorisationRepository $categorisationRepository, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Accès réservé aux administrateurs.');
        return $this->redirectToRoute('app_home');
        }
        
        $categorie = $categorisationRepository->find($id);
        if(!$categorie)
            {
                $this->addFlash('error', 'Cette catégorie n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }

            $entityManager->remove($categorie);
            $entityManager->flush();
            $this->addFlash('success', ' La catégorie a bien été supprimée');


            return $this->redirectToRoute('app_home');
    }
}
