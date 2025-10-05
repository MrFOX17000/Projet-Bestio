<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginatorInterface): Response
    {
        $data = $articleRepository->findAll();

                $articles = $paginatorInterface->paginate
                (
                $data,
                $request->query->getInt('page', 1),
                8 // Nombre d'éléments par page
                );

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }   

    #[Route('/new/article', name: 'new_article')]
    public function newArticle(Request $request, EntityManagerInterface $entityManager ): Response
    {
          $article = new Article();

            $formArticle = $this->createForm(ArticleType::class, $article);
            $formArticle->handleRequest($request);

            if ($formArticle->isSubmitted() && $formArticle->isValid()) {
                $user = $this->getUser();
                $article->setRedacteur($user);
                $article->setDateArticle(new DateTime());
                 $image = $formArticle->get('image')->getData(); //On récupère le fichier uploadé depuis le formulaire
                if ($image) {
                    $imageDirectory = $this->getParameter('images_directory'); // On récupère le chemin du dossier d'upload depuis le fichier services.yaml

                    $newFilename = uniqid().'.'.$image->guessExtension(); //On renomme chaque fichier pour éviter les conflits de noms
                    try {
                            $image->move($imageDirectory, $newFilename); //On déplace le fichier dans le dossier Public/Uploads
                        } catch (FileException $e) {
                        // Si l'upload rencontre un problème on affiche un message d'erreur
                        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                                
                        //Et on redirige vers le formulaire
                        return $this->redirectToRoute('new_article');
                        }
                        $article->setImage('/uploads/' . $newFilename); // On stocke le chemin relatif de l'image dans la base de données
            }

                $entityManager->persist($article);
                $entityManager->flush();

                $this->addFlash('success', 'L\'article a bien été ajouté');

                return $this->redirectToRoute('app_article');
            }

            return $this->render('article/new.html.twig', [
                'formArticle' => $formArticle->createView(),
            ]);
        }


    #[Route('/show/article/{id}', name: 'show_article')]
    public function show(int $id, Request $request, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé.');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
    
     #[Route('/delete/article/{id}', name: 'delete_article')]
    public function delete(int $id, Request $request, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        
            $article = $articleRepository->find($id);


            if (!$article) {
                $this->addFlash('warning', 'Cet article n\'existe pas.');
                return $this->redirectToRoute('app_article');
            }

           
                $entityManager->remove($article);
                $entityManager->flush();

                $this->addFlash('success', 'L\'article a bien été supprimée.');


           return $this->redirectToRoute('app_article');
    }  



}
