<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Form\GenreType;
use OpenApi\Annotations as OA;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/genres", name="api_genres_")
 * 
 * @OA\Tag(name="O'Flix API : Genres")
 * 
 */
class GenreController extends JsonController
{
    /**
     * Liste tout les genres
     * 
     * @Route("", name="browse", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns all the genres",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Genre::class, groups={"api_genres"}))
     *     )
     * )
     * 
     * @param GenreRepository $genreRepository
     * @return JsonResponse
     */
    public function showAllgenres(GenreRepository $genreRepository): JsonResponse
    {
        // TODO : le repository
        $genres = $genreRepository->findAll();
        // TODO : return json
        return $this->json(
            // data
            $genres,
            // CODE http
            Response::HTTP_OK,
            // pas d'entete supplémentaires
            [],
            // on spécifie les groupes de serialisations
            [
                "groups" => [
                    "api_genres"
                ]
            ]
        );

        //? avec notre méthode du JsonController
        /*
        return $this->json200($genres, ["api_genres"]);
        */
    }

    /**
     * Affiche les genres avec leur films
     * 
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id": "\d+"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns on genres by id",
     *     @OA\JsonContent(ref=@Model(type=Genre::class, groups={"show_genre"}))
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Genre not found"
     * )
     * 
     * @param Genre $genre
     * @return JsonResponse
     */
    public function genres(Genre $genre = null): JsonResponse
    {   
        // Gestion du paramConverter ?
        
        if ($genre === null) {
            return $this->json404("J'ai pas trouvé le genre par l'ID que tu m'as donné, essaie encore.");
        }

        return $this->json(
            // les données à serialiser
            $genre,
            // le HTTP status code, 200
            Response::HTTP_OK,
            // les entetes HTTP, par défault 
            [],
            // dans le context, on précise les groupes de serialisation
            // pour limiter les propriétés que l'on veut serialiser
            [
                "groups" => 
                [
                    "show_genre"
                ]
            ]
        );
    }

    /**
     * Affiche les films d'un genre
     * 
     * @Route("/{id}/movies", name="read_movies", methods={"GET"}, requirements={"id":"\d+"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns all movies from one genre",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Movie::class, groups={"show_genre"}))
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Genre not found"
     * )
     * 
     * @param Genre $genre
     * @return JsonResponse
     */
    public function genreMovies(Genre $genre = null)
    {   
        // Gestion du paramConverter
        if ($genre === null) {
            return $this->json404("J'ai pas trouvé le genre par l'ID que tu m'as donné, essaie encore.");
        }

        return $this->json(
            // les données à serialiser
            $genre->getMovies()
            ,
            // le HTTP status code, 200
            Response::HTTP_OK,
            // les entetes HTTP, par défault 
            [],
            // dans le context, on précise les groupes de serialisation
            // pour limiter les propriétés que l'on veut serialiser
            [
                "groups" => 
                [
                    "show_genre"
                ]
            ]
        );
    }

    /**
     * Affiche les genres avec leur films 
     * ? Ceci est un exemple, le mieux serait d'utiliser une route directe /movie/{id}
     * 
     * @Route("/{id}/movies/{idMovie}", name="movie_exemple", methods={"GET"}, requirements={"id":"\d+", "idMovie":"\d+"})
     * 
     * ici j'ai demandé 2 id dans ma route
     * je suis obligé de changer le nom d'un des paramètre de la route
     * Le paramConverter atteind sa limite, on passe en mode manuel
     * 
     * autre solution :
     * @link https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#2-fetch-via-an-expression
     */
    public function genreMovie(Genre $genre, int $idMovie)
    {   
        return $this->json(
            // les données à serialiser
            $genre->getMovies()
            ,
            // le HTTP status code, 200
            Response::HTTP_OK,
            // les entetes HTTP, par défault 
            [],
            // dans le context, on précise les groupes de serialisation
            // pour limiter les propriétés que l'on veut serialiser
            [
                "groups" => 
                [
                    "show_genre"
                ]
            ]
        );
    }

    /**
     * Creation de genre
     * 
     * @Route("", name="add", methods={"POST"})
     * 
     * @OA\RequestBody(
     *     @Model(type=GenreType::class)
     * )
     * 
     * @OA\Response(
     *     response=201,
     *     description="new created genre",
     *     @OA\JsonContent(
     *          ref=@Model(type=Genre::class, groups={"show_genre"})
     *      )
     * )
     * 
     * @param Request $request infos venant de mon front/utilisateur
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializerInterface Permet de transformer du JSON en Objet
     * @return JsonResponse
     */
    public function create(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializerInterface,
        ValidatorInterface $validator
        ): JsonResponse
    {
        // TODO : Request parce que je vais recevoir des données
        $jsonContent = $request->getContent();
        // dump($jsonContent);
        // je transforme ces données en Entité
        //! la deserialisation ne respecte aucune règle (Assert sur notre entité)
        //? notre serializer peut faire des erreurs son notre front/utilisateur nous envoi du JSON mal formé
        try { // on espère que le serializerInterface arrive à relire le JSON
            $genre = $serializerInterface->deserialize($jsonContent, Genre::class, 'json');
        } catch(Exception $e){ 
            //  si le serializerInterface n'arrive pas à relire le JSON on saute directement dans la partie Catch
            
            // erreur 422 : on ne peut pas traiter les infos qu'ils nous a donné
            return $this->json(
                "JSON mal formé",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        
        // dd($genre);
        
        // TODO : utiliser les Assert de notre entité pour valider la deserialisation
        // le validator nous renvoit la liste de toutes les erreurs
        $errorList = $validator->validate($genre);

        //je teste si il y a des erreurs 
        if (count($errorList) > 0){
            // j'ai des erreurs, l'utilisateur/front n'a pas respecter les Assert
            //? version bourrine : je transforme le tableau en chaine
            $errors = (string) $errorList;

            // erreur 422 : on ne peut pas traiter les infos qu'ils nous a donné
            return $this->json(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // TODO : entityManagerInterface pour l'enregistrement
        $em->persist($genre);
        $em->flush();
        // TODO : return json avec le bon code 201 (created)
        return $this->json(
            $genre,
            // je précise que tout est OK de mon coté en précisant que la cration c'est bien passé
            // 201
            Response::HTTP_CREATED,
            [],
            [
                "groups" => 
                [
                    "show_genre"
                ]
            ]
        );
    }
}
