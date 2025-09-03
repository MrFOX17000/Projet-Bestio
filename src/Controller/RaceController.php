<?php

namespace App\Controller;

use App\Entity\Race;
use App\Form\RaceType;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class RaceController extends AbstractController
{
      #[Route('/race', name: 'app_race')]
    public function addRace(Request $request, EntityManagerInterface $entityManager, RaceRepository $raceRepository): Response
    {
        $race = new Race;
        $formRace = $this->createForm(RaceType::class, $race);
        $formRace->handleRequest($request);
        if ($formRace->isSubmitted() && $formRace->isValid()) {
            $entityManager->persist($race);
            $entityManager->flush();
            $this->addFlash('success', 'Race ajoutée avec succès !');
            return $this->redirectToRoute('app_race');
        }
        return $this->render('race/index.html.twig', [
            'formRace' => $formRace->createView()
        ]);
    }

     #[Route('/delete/race/{id}', name: 'delete_race')]
    public function deleteRace(Request $request, EntityManagerInterface $entityManager, RaceRepository $raceRepository, int $id): Response
    {
        $race = $raceRepository->find($id);
        if(!$race)
            {
                $this->addFlash('error', 'Cette race n\'existe pas.');
                return $this->redirectToRoute('app_home');
            }

            $entityManager->remove($race);
            $entityManager->flush();
            $this->addFlash('success', ' La race a bien été supprimée');


            return $this->redirectToRoute('app_home');
    }
}
