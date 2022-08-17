<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

class OmdbApiObject 
{
    public $Title; //String
    public $Year; //String
    public $Rated; //String
    public $Released; //Date
    public $Runtime; //String
    public $Genre; //String
    public $Director; //String
    public $Writer; //String
    public $Actors; //String
    public $Plot; //String
    public $Language; //String
    public $Country; //String
    public $Awards; //String
    public $Poster; //String
    /**
     * ratings
     *
     * @var OmdbApiRating[]
     */
    public $Ratings; //array( Ratings )
    public $Metascore; //String
    public $imdbRating; //String
    public $imdbVotes; //String
    public $imdbID; //String
    public $Type; //String
    public $DVD; //Date
    public $BoxOffice; //String
    public $Production; //String
    public $Website; //String
    public $Response; //String

    public function __construct()
    {
        $this->Ratings = new ArrayCollection();
    }
}


