<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\OmdbApi;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/back/movie")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="app_back_movie_index", methods={"GET"})
     * 
     * ça fait doublon avec notre code :)
     * @link https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/security.html#isgranted
     * @IsGranted("ROLE_MANAGER")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(MovieRepository $movieRepository): Response
    {
        // TODO restreindre cette methode au ROLE_MANAGER
        // https://symfony.com/doc/current/security.html#securing-controllers-and-other-code
        // $this->denyAccessUnlessGranted('ROLE_MANAGER');
        
        return $this->render('back/movie/index.html.twig', [
            'movies' => $movieRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="app_back_movie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MovieRepository $movieRepository, OmdbApi $omdbapi, SluggerInterface $slugger): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Merci mon service 
            $poster = $omdbapi->fetchPoster($movie->getTitle());
            $movie->setPoster($poster);
            //TODO : générer le slug => fait dans le Listener
            // $slug = $slugger->slug($movie->getTitle());
            // $movie->setSlug($slug);

            $movieRepository->add($movie);
            // ajout d'un flash message
            // @link https://symfony.com/doc/current/controller.html#flash-messages
            $this->addFlash(
                'notice', // le type de message est une clé, on peut donc y mettre ce que l'on veux
                // on va pouvoir faire passer plusieurs message avec le même type
                'Votre film a bien été enregistrée.' // le message
            );
            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_movie_show", methods={"GET"})
     */
    public function show(Movie $movie): Response
    {
        return $this->render('back/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_back_movie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Movie $movie, MovieRepository $movieRepository, SluggerInterface $slugger): Response
    {
        // TODO je veux appliquer une restriction complexe, décrite dans un voter
        // je pose la question de la même manière qu'avec un rôle
        
        $this->denyAccessUnlessGranted("POST_EDIT", $movie);


        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO : générer le slug => fait dans le Listener
            // $slug = $slugger->slug($movie->getTitle());
            // $movie->setSlug($slug);
            $movieRepository->add($movie);
            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_movie_delete", methods={"POST"})
     */
    public function delete(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $movieRepository->remove($movie);
        }

        return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
    }
}
