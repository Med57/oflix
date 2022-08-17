<?php

namespace App\Controller\Api\Factice;

use App\Models\Factice\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/factice")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/movies", name="app_api_factice_movie", methods={"GET"})
     */
    public function index(): Response
    {
        $movie = new Movie();
        $movie->title = "Le Zoli Titre";
        $movie->summary = "ezafliughqziufhgeqgfbhzqbfgfv";
        $movie->rating = 3.8;
        return $this->json($movie);
    }
}
