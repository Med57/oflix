<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $genres = [
            1 => "Action",
            2 => "Animation",
            3 => "Aventure",
            4 => "Comédie",
            5 => "Dessin animé",
            6 => "Documentaire",
            7 => "Drame",
            8 => "Espionnage",
            9 => "Famille",
            10 => "Fantastique",
            11 => "Historique",
            12 => "Policier",
            13 => "Romance",
            14 => "Science-Fiction",
            15 => "Thriller",
            16 => "Western",
        ];
        
        // on parcours la liste de genres
        foreach ($genres as $value) {
            $genre = new Genre();
            $genre->setName($value);
            $manager->persist($genre);
        }

        $manager->flush();
    }
}
