<?php

namespace App\EventSubscriber;

use App\Repository\MovieRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    private $movieRepository;
    private $twig;

    /**
    * Constructor
    * @link https://symfony.com/doc/current/the-fast-track/en/12-event.html#implementing-a-subscriber
    */
    public function __construct(MovieRepository $movieRepository, Environment $twig)
    {
        $this->movieRepository = $movieRepository;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // TODO : pas tout le temps ...
        // dd($event);
        /*
        RandomMovieSubscriber.php on line 28:
            Symfony\Component\HttpKernel\Event\ControllerEvent {#1305 ▼
            -controller: array:2 [▼
                0 => App\Controller\Front\MovieController {#1226 ▶}
                1 => "showSlug"
            ]
            -kernel: Symfony\Component\HttpKernel\HttpKernel {#5054 ▶}
            -request: Symfony\Component\HttpFoundation\Request {#3 ▶}
            -requestType: 1
            -propagationStopped: false
            }
        */
        $controller = $event->getController();

        //! dans les cas où il y a des sub-request, cela nous renvoit un tableau de FQCN de controller
        // donc je ne récupère que le premier
        if (is_array($controller)){$controller = $controller[0];}

        // je demande le nom de la classe de cet objet
        // eg : App\Controller\Front\MovieController
        $nomController = get_class($controller);

        if (strpos($nomController, 'App\Controller\Front') === false){
            // je n'ai pas trouvé 'App\Controller\Front'
            // dans le nom du controller
            // je ne fait donc rien et je return
            return;
        }

        // TODO : Faire une requete custom pour obtenir un film aléatoire
        // On a besoin de MovieRepository, 
        // injection de dépendance, 
        // On hérite d'une interface, 
        // on peut pas modifier la méthode, 
        // on utilise le contructeur
        $randomMovieArray = $this->movieRepository->findRandomMovie();

        // TODO : Donner l'objet Movie à twig
        // d'après la doc : $this->twig->addGlobal('conferences', $this->conferenceRepository->findAll());
        $this->twig->addGlobal("randomMovie", $randomMovieArray);

    }

    public static function getSubscribedEvents()
    {
        // ici on retourne un tableau avec en clé un event
        // et en valeur le nom de la méthode qui sera éxecutées
        return [
            // ici on se palce avant l'appel au controller
            'kernel.controller' => 'onKernelController',
            
            // ça ne fonctionne pas car Twig fait son rendu avant la fin de la méthode du Controller
            // 'kernel.view' => 'onKernelController',

        ];
    }
}
