<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\MovieProvider;
use App\Entity\Actor;
use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Season;
use App\Models\Actor as ModelsActor;
use App\Service\OmdbApi;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class MovieFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * Mon magnifique service
     *
     * @var OmdbApi
     */
    private $omdbApi;

    /**
    * Service pour générer le slug d'un film
    *
    * @var SluggerInterface
    */
    public $slugger;
    

    public function __construct(OmdbApi $omdbApi, SluggerInterface $slugger)
    {
        $this->omdbApi = $omdbApi;
        $this->slugger = $slugger;
    }

    /**
     * cette méthod est hérité ET est abstract
     * je ne peut pas modifier sa signature
     * la signature d'une méthode ce sont ses paramètres et le type de retour
     * 
     * Donc si je veux utiliser l'injection de dépendance
     * j'utilise le constructeur
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {

        // création de l'instance de génération de Faker
        $faker = Factory::create('fr_FR');
        // on ajoute notre Movie Title Provider
        // cela rajoute nos méthodes de notre provider à $faker
        // $faker->getRandomMovieTitle()
        $faker->addProvider(new MovieProvider($faker));

        // on fait un copier/coller de notre méthode de notre controller Movie
        for ($i=0; $i < 300; $i++) { 
            $movie = new Movie();
            
            // on génère un bout de texte de 25 caractère pour simuler un titre
            //$movie->setTitle($faker->realText(25));
            // la génération aléatoire ne nous suffit pas, on utilise notre provider
            
            //$randomTitle = $faker->getRandomMovieTitle();
            $randomTitle = $faker->unique()->getRandomMovieTitle();
            $movie->setTitle($randomTitle);
            //TODO : générer le slug => fait dans le Listener
            //$slug = $this->slugger->slug($movie->getTitle());
            //$movie->setSlug($slug);

            // un sentence va contenir 6 mots, ce qui ressemble à un résumé
            $movie->setSummary($faker->sentence());
            // on génère un paragraphe            
            $movie->setSynopsis($faker->paragraph());

            // TODO : année random entre 1970 et 2020
            $date = new DateTimeImmutable();
            $date = $date->setDate(rand(1970,2020), rand(1,12), rand(1,28));

            // https://fakerphp.github.io/formatters/date-and-time/#datetimethiscentury
            // https://www.php.net/manual/fr/datetimeimmutable.createfrommutable.php
            $dateFake = DateTimeImmutable::createFromMutable($faker->dateTimeThisCentury());
            $movie->setReleasedAt($dateFake);

            // TODO : aléatoire entre 90 180 minutes
            $movie->setDuration(rand(90,180));
            // https://picsum.photos/id/{id}/200/300
            // $movie->setPoster("https://i.picsum.photos/id/316/200/300.jpg?hmac=sq0VBO6H0wGg9Prod7MVUUB_7B91kmD5E1X1TRSo66U");            
            // $movie->setPoster("https://picsum.photos/id/".rand(100, 1000)."/200/300");
            $movie->setPoster("https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg");
            //$poster = $this->omdbApi->fetchPoster($movie->getTitle());
            //$movie->setPoster($poster);
            
            $movie->setCountry("FR");
            
            // TODO : série OU film
            $typeSerieFilm = rand(1,2) == 1 ? "Film" : "Série";
            $movie->setType($typeSerieFilm);
            
            // l'ajout de season uniquement sur les séries
            if ($typeSerieFilm === "Série")
            {
                // On va créer un nombre aléatoire de Season entre 2 et 12
                for ($j=1; $j < rand(2,12); $j++) 
                { 
                    
                    $season = new Season();
                    // aléatoire 12-24
                    $season->setNbEpisode(rand(12,24));
                    // le titre est le numéro de saison
                    $season->setTitle("Saison ".$j);
                    //! Voir les erreurs dans le E07.md si on ne fait pas de persist
                    $manager->persist($season);

                    // je rajoute cette nouvelle saison à ma série
                    $movie->addSeason($season);
                }
            }

            // TODO : définir le genre du film
            // ? je pourrais récup tout les genre avec un findAll, puis en prendre un/plusieur au hasard
            // https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html#loading-the-fixture-files-in-order
            // ! Le PB, il FAUT ABSOLUMENT que le flush soit fait ici
            // ? on le fait avec l'ajout de la méthode getDependencies()
            // on peux les récup avec findAll()
            // le findAll est dans la class Repository, je doit donc la récup avant
            $genreRepository = $manager->getRepository(Genre::class);

            $allGenre = $genreRepository->findAll();
            // on prend un genre random
            $randomGenre = $allGenre[rand(0, count($allGenre)-1)];
            $movie->addGenre($randomGenre);

            $manager->persist($movie);
            // on garde trace de tout les films dans un tableau
            // pour une utilisation ultérieure
            $moviesList[] = $movie;
        }

        /******* Génération des Actors ************/
        for ($i=0; $i < 1500; $i++) { 
            $actor = new Actor();
            $actor->setFirstName($faker->firstname());
            $actor->setLastName($faker->lastname());

            $manager->persist($actor);
            // on garde trace de tout les actor dans un tableau
            // pour une utilisation ultérieure
            $actorList[] = $actor;
        }

        // TODO : creation casting
        for ($i=0; $i < 1400; $i++) { 
            $casting = new Casting();
            $casting->setRole($faker->name());
            $casting->setCreditOrder(rand(1,4));

            // TODO : Relation avec Movie
            // ? je pourrais récup tout les films avec un findAll, puis en prendre un au hasard
            // ! Le PB, c'est que le flush n'est pas encore fait ici

            // grâce à mon tableau de movie, je peux y accèder avec un index
            // cet index commence à 0, et fini a X
            // X = le nombre de films -1 car on commence à zéro
            $randomMovie = $moviesList[rand(0, count($moviesList)-1)];

            $casting->setMovie($randomMovie);
            
            // TODO : relation avec Actor
            $randomActor = $actorList[rand(0, count($actorList)-1)];
            $casting->setActor($randomActor);

            // TODO : persist
            $manager->persist($casting);

        }
        // C'est que à partir d'ici que l'on aura nos données en BDD
        $manager->flush();
    }

    /**
     * Utile pour donner un ordre d'execution aux fixtures
     */
    public function getDependencies()
    {
        // On veux absolument que les fixture de Genre soit éxecuté AVANT
        return [
            GenreFixture::class,
        ];
    }
}
