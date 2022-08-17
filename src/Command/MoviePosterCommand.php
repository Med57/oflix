<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Service\OmdbApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoviePosterCommand extends Command
{
    protected static $defaultName = 'app:movie:poster';
    protected static $defaultDescription = 'Fetch Poster From OmdbAPI';

    public const DEFAULT_POSTER_URL = "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg";

    private $movieRepository;
    private $omdbApi;
    private $entityManager;

    public function __construct(MovieRepository $movieRepository, OmdbApi $omdb, EntityManagerInterface $em)
    {
        //! ne pas ouvblier d'apeller le parent, il parait qu'il y a des trucs obligatoire là bas
        parent::__construct();
        
        $this->movieRepository = $movieRepository;
        $this->omdbApi = $omdb;
        $this->entityManager = $em;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Omdb API FetchPoster bonjour :)");
        
        // TODO : movieRepository
        // $movies = $this->movieRepository->findAll();
        //? on pourrait penser à ne prendre que les films qui n'ont pas de poster (findBy()) OU le poster par défaut
        $movies = $this->movieRepository->findBy(["poster" => MoviePosterCommand::DEFAULT_POSTER_URL]);
        // TODO : boucle sur la liste de film
            
        foreach ($movies as $movie) {
            // TODO : notre service OmdbAPi
            $urlPoster = $this->omdbApi->fetchPoster($movie->getTitle());
            $movie->setPoster($urlPoster);
            $io->info("[" . $movie->getTitle() . "] a été mise à jour.");
            // le but est de tester, mais pas sur les 300 
            
            //? pour les tests sans utiliser trop l'ApiKey
            $this->entityManager->flush();
            dd($movie->getPoster());
        }
        
        // TODO : EM : flush
        $this->entityManager->flush();

        $io->success('Bravo !!');

        return Command::SUCCESS;
    }
}
