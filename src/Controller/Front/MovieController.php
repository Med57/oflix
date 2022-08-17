<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Repository\CastingRepository;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/", name="app_movie")
     */
    public function index(MovieRepository $movieRepository, GenreRepository $genreRepository): Response
    {
        // TODO : se connecter à la BDD
        //? on demande à symfony de nous donner la BDD
        
        // TODO : récup tout les movies
        $movies = $movieRepository->findAll();
        
        // TODO : récup tout les Genres
        $genres = $genreRepository->findAll();

        // TODO : donner les movies à twig
        return $this->render('movie/home.html.twig', 
        [
            "movies" => $movies,
            "genres" => $genres
        ]);
    }

    /**
     * @Route("/movie_param_conv_slug/{slug}", name="app_movie_show_pc_slug")
     */
    public function showPCSlug(Movie $movie = null): Response
    {
        dd($movie);
        // Si le ParamConverter ne trouve pas d'objet pour l'id fournit
        // on lui porpose une solution alternative : la valeur null
        // cela nous permet de détecter l'erreur et de la gérer à notre manière
        if ($movie === null) {
            return $this->redirectToRoute('app_movie');
        }
        // TODO : se connecter à la BDD
        //? on demande à symfony de nous donner la BDD
        
        // TODO : donner ce movie à twig
        return $this->render('movie/show.html.twig', 
        [
            'controller_name' => 'MovieController',
            "movie" => $movie
        ]);
    }
    /**
     * @Route("/movie/{slug}", name="app_movie_show_slug")
     * exemple de requirement qui fonctionnera pour le film 300
     * requirements={"slug": "\w+(-\w*)*"}
     * \w matches any word character (equivalent to [a-zA-Z0-9_])
     */
    public function showSlug(string $slug, MovieRepository $movieRepository, CastingRepository $castingRepository): Response
    {
        // TODO : pas d'id
        $movie = $movieRepository->findOneBy(["slug"=>$slug]);
        // dump($movie);

        if ($movie === null){
            // TODO : pas d'id
            throw $this->createNotFoundException("Le film de slug [".$slug."] n'existe pas.");
        }

        $castingsOrdered = $castingRepository->findBy(
            [ // en SQL on ferait WHERE movie_id = 5
            // ? on parle avec doctrine via les entité pas via les colonnes
                "movie" => $movie->getId()
            ],
            [ // en SQL on ferait ORDER BY column DESC/ASC
                "creditOrder" => "ASC"
            ]
        );
        // dump($castingsOrdered);
        
        return $this->render('movie/show.html.twig', 
        [
            'controller_name' => 'MovieController',
            "movie" => $movie
            // je fournit les casting ordonnés, je n'utiliserai pas la relation
            ,"castings" => $castingsOrdered
        ]);
    }

    /**
     * @Route("/movie-old/{id}", name="app_movie_show_old_do_not_use", requirements={"id":"\d+"})
     */
    public function show(int $id, MovieRepository $movieRepository, CastingRepository $castingRepository): Response
    {
        // TODO : se connecter à la BDD
        //? on demande à symfony de nous donner la BDD
        
        // TODO : récup tout 1 movie
        $movie = $movieRepository->find($id);
        //dump($movie);
        

        // TODO : tester si le movie existe
        // la méthode find() nous renvoit null si l'objet n'existe pas
        if ($movie === null){
            // TODO : renvoit d'une 404
            // la version spartiate
            // return new Response("Le film d'id ".$id." n'existe pas.", Response::HTTP_NOT_FOUND);

            // symfony nous propose d'utiliser une méthode qui va générer une vue spéciale
            // qui va afficher une page suivant l'erreur
            // ici on demande une erreur NOT FOUND càd 404
            // on LANCE l'erreur pour quelqu'un l'attrape
            throw $this->createNotFoundException("Le film d'id ".$id." n'existe pas.");
        }

        // TODO : je veux les castings dans l'ordre du creditOrder
        //? si j'utilise la relation, ce n'est pas moi qui fait la requte, je ne maitrise donc pas l'ordre
        //? on va donc utiliser le repository de Casting pour nous même aller chercher les castings dans le bon ordre
        
        $castingsOrdered = $castingRepository->findBy(
            [ // en SQL on ferait WHERE movie_id = 5
            // ? on parle avec doctrine via les entité pas via les colonnes
                "movie" => $movie->getId()
            ],
            [ // en SQL on ferait ORDER BY column DESC/ASC
                "creditOrder" => "ASC"
            ]
        );
        // dump($castingsOrdered);
        // Autre Méthode avec QueryBuilder
        //$qb = $castingRepository->findByMovieOrderedByCreditOrder($movie);
        //dump($qb);

        // Autre méthode avec DQL
        //$dql = $castingRepository->findByMovieOrderedByCreditOrderDQL($movie);
        //dump($dql);

        // Optimisation pour les casting via QueryBuilder
        //$actors = $castingRepository->findAllJoinedToPersonByMovieQb($movie);
        //dump($actors);

        // TODO : donner ce movie à twig
        return $this->render('movie/show.html.twig', 
        [
            'controller_name' => 'MovieController',
            "movie" => $movie
            // je fournit les casting ordonnés, je n'utiliserai pas la relation
            ,"castings" => $castingsOrdered
        ]);
    }

    /**
     * @Route("/movie_param_conv/{id}", name="app_movie_show_pc", requirements={"id":"\d+"})
     */
    public function showPC(Movie $movie = null): Response
    {
        // Si le ParamConverter ne trouve pas d'objet pour l'id fournit
        // on lui porpose une solution alternative : la valeur null
        // cela nous permet de détecter l'erreur et de la gérer à notre manière
        if ($movie === null) {
            return $this->redirectToRoute('app_movie');
        }
        // TODO : se connecter à la BDD
        //? on demande à symfony de nous donner la BDD
        
        // TODO : donner ce movie à twig
        return $this->render('movie/show.html.twig', 
        [
            'controller_name' => 'MovieController',
            "movie" => $movie
        ]);
    }

    /**
     * Création de film : semi-auto
     * @Route("/movie/create", name="app_movie_create")
     */
    public function create(EntityManagerInterface $entityManagerInterface): Response
    {
        // TODO : connexion à la BDD
        //? on utilise EntityManagerInterface pour pouvoir inserer/modifier la BDD

        // TODO : un nouveau film
        $movie = new Movie();
        
        $movie->setTitle("La fin de journée c'est dur, comme les oeufs");
        $movie->setSummary("Hokuto No Ken");
        $movie->setSynopsis("Tu es mort mais tu ne le sais pas encore");
        $movie->setReleasedAt(new DateTimeImmutable());
        $movie->setDuration(159);
        $movie->setPoster("https://i.picsum.photos/id/316/200/300.jpg?hmac=sq0VBO6H0wGg9Prod7MVUUB_7B91kmD5E1X1TRSo66U");
        $movie->setCountry("FR");
        $movie->setType("Film");

        // TODO : demander à doctrine d'inserer le film en BDD
        // ! Il faut d'abord demander à EntityManagerInterface de prendre connaissance de notre nouvel objet
        $entityManagerInterface->persist($movie);
        //! notre BDD n'est pas au courant de nos actions
        //? aucune requete n'a été faites
        dump($movie);

        // ! Maintenant que E.M. connait cette objet, on peut lui demander de mettre à jour la BDD
        $entityManagerInterface->flush();
        // ? touts les objets (persist) sont inserés en BDD
        // ? toutes les requetes sont éxécutées
        dd($movie);

        // on redirige l'utilisateur sur une route donné : app_movie
        return $this->redirectToRoute("app_movie");
    }

    /**
     * Mies à jour d'un Movie
     * 
     * @Route("/movie/update/{id}", name="app_movie_update", requirements={"id":"\d+"})
     * 
     * @param integer $id
     * @param EntityManagerInterface $em
     * @param MovieRepository $repo
     * @return Response
     */
    public function update(int $id, EntityManagerInterface $em, MovieRepository $repo): Response
    {
        // Je doit tout d'abord aller chercher la dernière information connue de la BDD
        // TODO : find($id)
        $movie = $repo->find($id);

        // TODO : mettre à jour les infos dans l'instance
        $movie->setRating(9.9);

        //? pas besoin de persist() car $em connait déjà l'instance
        // TODO : flush()
        $em->flush();

        // TODO : redirect
        return $this->redirectToRoute('app_movie_show_slug',
        [
            "slug" => $movie->getSlug()
        ]
        );
    }

    /**
     * Suppression d'un film
     *
     * @Route("/movie/delete/{id}", name="app_movie_delete", requirements={"id":"\d+"})
     * 
     * @param Movie $movie
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Movie $movie, EntityManagerInterface $em): Response
    {
        // TODO : BDD => EM
        $em->remove($movie);
        //! ne pas oublier d'appliquer les modifications en BDD avec flush()
        $em->flush();
        // TODO : redirect
        return $this->redirectToRoute("app_movie");
    }
}
