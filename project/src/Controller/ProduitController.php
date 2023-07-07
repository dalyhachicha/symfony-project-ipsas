<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Reviews;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ProduitController extends AbstractController
{
    private $client;

    public function __construct(SymfonyHttpClientInterface $client) // Updated type hint
    {
        $this->client = $client;
    }
    #[Route('/produit', name: 'List_Produit')]
    public function index(ProduitRepository $produitRepository,EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $filters = [
            'name' => $request->query->get('name'),
            'category' => $request->query->get('category'),
            'min_price' => $request->query->get('min_price'),
            'max_price' => $request->query->get('max_price')
        ];
        $pagination = $paginator->paginate(
            $produitRepository->paginationQuery($filters),
            $request->query->get('page', 1),
            8
        );
        
        return $this->render('produit/index.html.twig', [
            'pagination' => $pagination
        ]);
        // $pagination = $paginator->paginate(
        //     $produitRepository->paginationQuery(),
        //     $request->query->get('page', 1),
        //     8
        // );
    
        // return $this->render('produit/index.html.twig', [
        //     // 'listeProduits' => $Listeproducts,
        //     'pagination' => $pagination
        // ]);
    }


    #[Route('/produit/{id}', name: 'show_produit', methods: ['GET', 'POST'])]
    public function showProduit(ProduitRepository $produitRepository, EntityManagerInterface $entityManager , Request $request, $id)
    {
        $produit = $produitRepository->findBy(['id' => $id]);
        if ($request->isMethod('POST')) {
            $rating = $request->request->get('rate');
            $comment = $request->request->get('w3review');
            // Assuming you have a Reviews entity class and a ReviewsRepository to persist the review
            $review = new Reviews();
            $review->setNote($rating);
            $review->setCommentaire($comment);
            $review->setProduit($produit[0]);
            
            $entityManager->persist($review);
            $entityManager->flush();
            
            // Redirect to the same page after adding the review
            return $this->redirectToRoute('show_produit', ['id' => $id]);
        }
        return $this->render('produit/show.html.twig', [
            'produit' => $produit[0]
        ]);
    }
}
