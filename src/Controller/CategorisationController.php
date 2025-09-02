<?php

namespace App\Controller;

use App\Entity\Categorisation;
use App\Form\CategorisationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategorisationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategorisationController extends AbstractController
{

      #[Route('/categorisation', name: 'app_categorisation')]
    public function addCategorisation(Request $request, EntityManagerInterface $entityManager, CategorisationRepository $categoRepository): Response
    {
        $categorisation = new Categorisation;
        $formCategorisation = $this->createForm(CategorisationType::class, $categorisation);
        $formCategorisation->handleRequest($request);
        if ($formCategorisation->isSubmitted() && $formCategorisation->isValid()) {
            $entityManager->persist($categorisation);
            $entityManager->flush();
            $this->addFlash('success', 'Catégorisation ajoutée avec succès !');
            return $this->redirectToRoute('app_categorisation');
        }
        return $this->render('categorisation/index.html.twig', [
            'formCategorisation' => $formCategorisation->createView()
        ]);
    }
}
