<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Models\CustomJsonError;
use OpenApi\Annotations as OA;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * 
 * @Route("/api/movies", name="api_movies_")
 * 
 * @OA\Tag(name="O'Flix API : Movies")
 */
class MovieController extends JsonController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns all the movies",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Movie::class, groups={"show_movie"}))
     *     )
     * )
     */
    public function movies(MovieRepository $repo): Response
    {
        $allMovies = $repo->findAll();

        return $this->json200(
            // les données à serialiser
            $allMovies,
            // les groupes de sérialisation
            ["show_movie"]
        );
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id":"\d+"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns one the movie",
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"show_movie"}))
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Movie not found"
     * )
     */
    public function movie(Movie $movie = null): Response
    {
        // SI le paramConverter n'a pas trouvé de Movie correspondant au paramètre id
        // $movie est null
        if ($movie === null) {
            //! si on devait renvoyer du HTML
            // throw $this->createNotFoundException("Le film avec cet id n'existe pas.");
            //on doit renvoyer du JSON
            return $this->json404("pas de film avec cet ID");
        }

        return $this->json200($movie, ["show_movie"]);
    }

    /**
     * Creation de Film
     *
     * @Route("", name="add", methods={"POST"})
     * 
     * @OA\RequestBody(
     *     @Model(type=MovieType::class)
     * )
     * 
     * @OA\Response(
     *     response=201,
     *     description="new created movie",
     *     @OA\JsonContent(
     *          ref=@Model(type=Movie::class, groups={"show_movie"})
     *      )
     * )
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createItem(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator
        ): JsonResponse
    {
        // Récupérer le contenu JSON
        $jsonContent = $request->getContent();
        // Désérialiser (convertir) le JSON en entité Doctrine Movie
        //? Si on reçoit un objet dans une relation (ici les genres)
        //? Automatiquement le serializer va demander si un denormalizer sait faire
        //? comme on a créer notre DoctrineDenormalizer, celui ci va être appellé, et il va répondre Oui
        //? on va donc avoir un find() qui sera fait en auto 💪
        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');
        
        /* dump($movie);
            App\Entity\Movie {#2787 ▼
            -id: null
            -title: "Apollo E19"
            -summary: "string"
            -synopsis: "string"
            -releasedAt: DateTimeImmutable @1650612254 {#2903 ▶}
            -duration: 0
            -poster: "string"
            -country: "string"
            -rating: 0.0
            -seasons: Doctrine\Common\Collections\ArrayCollection {#2786 ▶}
            -type: "string"
            -genres: Doctrine\Common\Collections\ArrayCollection {#2783 ▼
                -elements: array:1 [▼
                0 => App\Entity\Genre {#6407 ▼
                    -id: 474
                    -name: "Documentaire"
                    -movies: Doctrine\ORM\PersistentCollection {#4286 ▶}
                }
                ]
            }
            -castings: Doctrine\Common\Collections\ArrayCollection {#2782 ▶}
            -reviews: Doctrine\Common\Collections\ArrayCollection {#2721 ▶}
            -slug: null
            -updatedAt: null
            }
        */
        // Valider l'entité
        // @link : https://symfony.com/doc/current/validation.html#using-the-validator-service
        $errorsList = $validator->validate($movie);
        // Y'a-t-il des erreurs ?
        if (count($errorsList) > 0) {
            // TODO Retourner des erreurs de validation propres
            //? version 1 bourrine : je transforme le tableau en chaine
            // $errors = (string) $errorsList;

            //? 2eme version avec mon objet customJsonError
            /* 
                $myCustomError = new CustomJsonError();
                $myCustomError->setErrorValidation($errorsList);
                $myCustomError->errorCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $myCustomError->message = "Erreur(s) sur la validation de l'objet";
            */
            
            // 3eme version avec une méthode dans mon parent : merci P.A.
            return $this->json422($errorsList);
        }
        // On sauvegarde l'entité
        $em->persist($movie);
        $em->flush();

        // TODO : return 201
        return $this->json(
            $movie,
            // je précise que tout est OK de mon coté en précisant que la création c'est bien passé
            // 201
            Response::HTTP_CREATED,
            [],
            [
                "groups" => 
                [
                    "show_movie"
                ]
            ]
        );
    }

    /**
     * Renvoit un film aléatoire
     * 
     * @Route("/random", name="random", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns random movie",
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"show_movie"}))
     * )
     * 
     * @param MovieRepository $movieRepository
     * @return JsonResponse
     */
    public function randomMovie(MovieRepository $movieRepository): JsonResponse
    {
        die("Tu es passé par ici");
        // TODO : repository
        // TODO : on a pas une requete custom pour ça ?
        $movieArray = $movieRepository->findRandomMovie();
        dd($movieArray);
        // je vais chercher le "vrai" film avec son entite
        $movie = $movieRepository->find($movieArray["id"]);
        
        // TODO : return json
        return $this->json200($movie, ["show_movie"]);
    }
}
