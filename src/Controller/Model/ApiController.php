<?php

namespace App\Controller\Model;

use App\Models\MoviesModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Model API Controller
 * @Route("/model")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="app_api")
     */
    public function index(): Response
    {
        // Pour twig
        /*
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
        */
        // TODO : récupérer des données
        $movieModel = new MoviesModel();
        $allMovies = $movieModel->getAllMovies();
        // TODO : les envoyer en json
        return $this->json($allMovies);
    }
}
