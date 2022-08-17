<?php

namespace App\Controller\Front;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/genre/{id}", name="app_genre", requirements={"id":"\d+"})
     */
    public function index(Genre $genre, GenreRepository $genreRepository): Response
    {
        return $this->render('movie/list.html.twig', [
            "movies" => $genre->getMovies(),
            "genres" => $genreRepository->findAll()
        ]);
    }
}
