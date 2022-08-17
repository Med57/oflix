<?php

namespace App\Service;

use App\Entity\Movie;
use App\Repository\MovieRepository;

class RatingCalculator
{
    /* en fait je ne fait pas la mise à jour donc pas besoin de MovieRepository
    private $movieRepository;

    public function __construct(MovieRepository $mr)
    {
        $this->movieRepository = $mr;
    }
    */

    /**
     * je demande le movie actuel plus le nouveau rating
     *
     * @param Movie $movie
     * @param integer $newReviewRating Pour faire le calcul avant le flush()
     * @return integer
     */
    public function Calculate(Movie $movie, int $newReviewRating) : int
    {
        $allReviews = $movie->getReviews();
        // TODO : 4+4+5 / 3 ==> moyennne
        // $cumulRating / $nbReview
        $cumulRating = $newReviewRating;
        // plus 1 car on a pas la dernière review en BDD
        $nbReview = count($allReviews)+1; 
        // donc pas de div par zéro

        foreach ($allReviews as $review) {
            $cumulRating += $review->getRating();
        }

        $newRating = $cumulRating / $nbReview;
        // on arrondi
        $newRating = round($newRating, 2);
        
        return $newRating;
    }
}