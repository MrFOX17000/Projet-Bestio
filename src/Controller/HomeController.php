<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategorisationRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategorisationRepository $categorisationRepository): Response
    {
        $categories = $categorisationRepository->findAll();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }

}
