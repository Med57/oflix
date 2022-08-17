<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Service\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

class MovieSlugCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:movie:slug';

    protected static $defaultDescription = 'Generate Movie Slug';

    private $movieRepository;
    private $slugger;
    private $entityManagerInterface;

    public function __construct(MovieRepository $movieRepository, MySlugger $slugger, EntityManagerInterface $em)
    {
        // je fait appel au constructeur parent pour être `correctly initialized`
        parent::__construct();

        $this->movieRepository = $movieRepository;
        $this->slugger = $slugger;
        $this->entityManagerInterface = $em;

    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // https://symfony.com/doc/5.4/console.html#console-output
        // version spartiate .. pas de couleur
        /* $output->writeln([
            'Movie Slug Generator',
            '============',
            '',
        ]);
        */

        // https://symfony.com/doc/current/console/style.html#basic-usage
        $inputOutput = new SymfonyStyle($input, $output);

        // TODO : dire bonjour
        $inputOutput->title("Bienvenue dans la génération de slug");

        // TODO : Aller chercher les films => repos
        $movies = $this->movieRepository->findAll();
        // https://symfony.com/doc/current/console/style.html#progress-bar-methods
        // je donne la valeur max de la progressbar
        $inputOutput->progressStart(count($movies));

        // TODO : boucle sur les films, setSlug ==> SluggerInterface
        foreach ($movies as $movie) {
            $slug = $this->slugger->slug($movie->getTitle());
            // TODO : option pour activer le lower : dans le fichier .env
            //! le preUpdate de doctrine ne seras PAS lancé car on ne modifie rien dans notre entity
            //! pour l'optimisation, doctrine ne fait que des requetes que si besoin
            //! donc si on ne touche pas à notre entity, doctrine ne ferat pas d'update, donc pas d'event
            $movie->setSlug($slug);
            
            // j'avance de 1 pas
            $inputOutput->progressAdvance();
            // parce que je veux voir la bar avancer
            //! NE JAMAIS FAIRE CA !!! Juste pour le délire
            // https://www.php.net/manual/fr/function.usleep.php
            // j'arrete l'éxecution pendant 250.000 microsecondes (0.25s)
            // usleep(250000);

            // TODO : petit message de résussite pour chaque film
            // $inputOutput->note($movie->getTitle() . " a été sluggifer.");
        }
        
        // TODO : flush
        $this->entityManagerInterface->flush();
    
        $inputOutput->success("YATTA !!");

        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}
