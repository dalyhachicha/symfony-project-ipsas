<?php

namespace App\Controller;

use App\Entity\Reviews;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewsController extends AbstractController
{
    #[Route('/reviews', name: 'app_reviews')]
    public function index(): Response
    {
        return $this->render('reviews/index.html.twig', [
            'controller_name' => 'ReviewsController',
        ]);
    }

    #[Route('/save-review', name: 'save_review', methods: ['POST'])]
    public function saveReview(Request $request): Response
    {
        $rating = $request->request->get('rate');
        $comment = $request->request->get('w3review');

        // Assuming you have a Reviews entity class
        $review = new Reviews();
        $review->setCommentaire($rating);
        $review->setCommentaire($comment);

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        // Redirect or render a response after saving the review
        return $this->redirectToRoute('app_reviews');
    }
}
