<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategorisationRepository;
use App\Repository\EspeceRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategorisationRepository $categorisationRepository, EspeceRepository $especeRepository): Response
    {
        $categories = $categorisationRepository->findAll();

        // Récupère le nombre total d'espèces
        $count = $especeRepository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Calcule l'offset pour l'animal du jour
        $seed = (int) (new \DateTime('today'))->format('Ymd');
        srand($seed);
        $offset = rand(0, max(0, $count - 1));

        // Récupère l'espèce du jour avec ses relations en un seul fetch join
        $qb = $especeRepository->createQueryBuilder('e')
            ->leftJoin('e.classe', 'c')->addSelect('c')
            ->leftJoin('c.appartenir', 'cat')->addSelect('cat')
            ->setFirstResult($offset)
            ->setMaxResults(1);

        $animalDuJour = $qb->getQuery()->getOneOrNullResult();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'animalDuJour' => $animalDuJour,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

}
