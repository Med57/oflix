<?php

namespace App\Controller\Front;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\MovieRepository;
use App\Service\RatingCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    /**
     * @Route("/movie/{id}/review", name="app_review_movie", requirements={"id":"\d+"})
     */
    public function index(
        int $id,
        MovieRepository $movieRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        RatingCalculator $ratingCalculator
        ): Response
    {
        // dd($request);
        // je récupère le Movie concerné
        $movie = $movieRepository->find($id);

        // TODO : créer le formulaire de review
        $newReview = new Review();
        // pour créer un formulaire, je doit faire appel à la méthode createForm()
        // celle ci est déjà présente dans mon controller ($this->)
        // car mon controller hérite de la classe AbstractController
        $form = $this->createForm(ReviewType::class, $newReview);

        // TODO : demande au formulaire :
        // gérer la requete -> il va lire les donnée reçues et les mettre dans un objet
        $form->handleRequest($request);
        
         // gestion du formulaire quand il est remplit : isSubmitted
         // valider les assert : isValid
        if ($form->isSubmitted() && $form->isValid()) { 
            // je debug avant de continuer : dd()
            // dd($newReview);
            // TODO : insertion en BDD
            // j'ai l'objet remplit par le formulaire
            
            // l'association avec le movie 
            $newReview->setMovie($movie);
            // persist et flush
            $entityManagerInterface->persist($newReview);
            
            // le double flush est bizarre, on s'économise en donnant le dernier rating à notre service RatingCalculator
            // $entityManagerInterface->flush();

            // TODO : calcul du nouveau rating du film
            $newRating = $ratingCalculator->Calculate($movie, $newReview->getRating());
            $movie->setRating($newRating);
            // BDD à jour
            $entityManagerInterface->flush();

            // je donne l'information à mon utilisateur que tout c'est bien passé
            //! je fait un redirect, je n'ai donc pas la possibilité de faire passer une variable à la methode render()
            //? on va donc utiliser la session
            // Symfony nous propose une gestion simplifié de la session
            // spécifique pour ce genre de message d'information : les flash messages
            // @link https://symfony.com/doc/current/controller.html#flash-messages
            $this->addFlash(
                'notice', // le type de message est une clé, on peut donc y mettre ce que l'on veux
                // on va pouvoir faire passer plusieurs message avec le même type
                'Votre critique a bien été enregistrée.' // le message
            );

            // redirect
            return $this->redirectToRoute("app_movie_show_slug", ["slug"=>$movie->getSlug()]);
        }
       
        
        // TODO : afficher : renderForm
        return $this->renderForm('review/index.html.twig', [
            "reviewForm" => $form,
            "movie" => $movie
        ]);
    }
}
