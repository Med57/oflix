<?php

namespace App\Controller\Front;

use App\Models\MoviesModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller qui utilise le Model
 * @Route("/model", name="model_")
 */
class ModelController extends AbstractController
{
    /**
     * Méthode qui affiche la page par défaut
     * 
     * @Route("/", name="default_page")
     *
     * @return Response
     */
    public function home(): Response
    {
        $number = random_int(0, 100);

        // sans twig : 
        // return new Response('<html><body>Lucky number: '.$number.'</body></html>');

        // avec twig
        return $this->render(
            // le chemin du fichier twig
            'model/home.html.twig',
            // les informations/variables nécessaire à la génération de la vue
            [
                "lucky_number" => $number
            ]
        );  
    }

    /**
     * méthode qui affiche la liste des films
     *
     * @Route("/list", name="liste_page")
     * 
     * @return Response
     */
    public function list(): Response
    {
        // TODO : instance du model
        $movieModel = new MoviesModel();
        // TODO : méthode qui me renvoit TOUT les films
        $listAllMovies = $movieModel->getAllMovies();
        // dump($listAllMovies);
        // TODO : fournir la liste à twig
        // TODO : dynamiser
        return $this->render(
            // le chemin du fichier twig
            'model/list.html.twig',
            // les informations/variables nécessaire à la génération de la vue
            [
                "movies" => $listAllMovies
            ]
        );  
    }

    /**
     * méthode qui affiche le détail d'un film
     *
     * @Route("/show/{id}", name="show_movie", requirements={"id": "\d+"})
     * 
     * @return Response
     */
    public function show(int $id): Response
    {
        // TODO : récupérer le film demandé
        // TODO : je dois aller lire le fichier Models\data.php
        //! en symfony on travaille avec des objets
        //! on va donc changer notre fichier data en objet

        // TODO : faire un new de cet objet
        $listMovies = new MoviesModel();
        // TODO : créer une méthode pour récupérer un film par son id
        
        $movieArray = $listMovies->getMovie($id);
        // dump( get_defined_vars() );
        // dump($movieArray);
        /*
        ^ array:8 [▼
            "type" => "Série"
            "title" => "Game of Thrones"
            "release_date" => 2011
            "duration" => 52
            "summary" => "Neuf familles nobles se battent pour le contrôle des terres de Westeros, tandis qu'un ancien ennemi revient..."
            "synopsis" => "Il y a très longtemps, à une époque oubliée, une force a détruit l'équilibre des saisons. Dans un pays où l'été peut durer plusieurs années et l'hiver toute une ▶"
            "poster" => "https://m.media-amazon.com/images/M/MV5BYTRiNDQwYzAtMzVlZS00NTI5LWJjYjUtMzkwNTUzMWMxZTllXkEyXkFqcGdeQXVyNDIzMzcwNjc@._V1_SX300.jpg"
            "rating" => 4.7
            ]
        */
        // TODO : fournir le film à twig pour qu'il l'affiche
        // TODO : modifier la page twig
        return $this->render(
            // le chemin du fichier twig
            'model/show.html.twig',
            // les informations/variables nécessaire à la génération de la vue
            [
                "movie" => $movieArray
            ]
        );  
    }

    /**
     * méthode qui affiche les films favoris de l'utilisateur
     *
     * @Route("/favorites", name="my_favorites")
     * 
     * @return Response
     */
    public function favorites(): Response
    {
        return $this->render(
            // le chemin du fichier twig
            'model/favorites.html.twig',
            // les informations/variables nécessaire à la génération de la vue
            [
                
            ]
        );  
    }

    /**
     * change le theme de l'utilisateur
     * 
     * @Route("/theme", name="theme_switcher")
     * 
     * @param SessionInterface $session
     * @link https://symfony.com/doc/current/components/http_foundation/sessions.html
     * @return Response
     */
    public function themeSwitcher(SessionInterface $session, MoviesModel $model): Response
    {
        // un exemple d'injection de dépendance
        // dump($model);

        // PHP se sert d'un tableau assoc pour les infos de session : $_SESSION
        //! en symfony tout doit être objet
        //? symfony devrait nous fournir un objet pour accéder à la session

        // Quand on veux demander à symfony une classe/interface
        // nous avons à notre disposiion le mécanisme d'injection de dépendance

        // mon code DEPEND d'une classe/interface que symfony me fournit
        // je vais donc demander à symfony de l'injecter dans ma function
        // injecter --> passer en paramètre


        // TODO : demander la session à symfony
        // injecter/passer en paramètre l'objet session 

        // TODO : stocker dans la clé 'theme' le nom du thème choisit
        // Si aucun theme, mettre le theme 'netflix'
        // sinon changer de theme entre 'netflix' et 'allocine'
        

        $currentTheme = $session->get('theme'); // $_SESSION["theme"]
        // dd($currentTheme); --> null
        if ($currentTheme === null){
            // je met 'netflix' comme valeur avec la clé 'theme'
            $session->set('theme', 'netflix');
        } else {
            // j'ai une valeur, je regarde laquelle pour changer
            if ($currentTheme === 'netflix') {
                // j'ai 'netflix', je passe en 'allocine'
                $session->set('theme', 'allocine');
            } else {
                // donc j'ai 'allocin"' je repasse en 'netflix'
                $session->set('theme', 'netflix');
            }
        }
        //dd($session);
        /*
        Symfony\Component\HttpFoundation\Session\Session {#218 ▼
        #storage: Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage {#219 ▼
            #bags: array:2 [▼
            "attributes" => Symfony\Component\HttpFoundation\Session\SessionBagProxy {#224 ▼
                -bag: Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag {#223 ▼
                -name: "attributes"
                -storageKey: "_sf2_attributes"
                #attributes: &1 array:1 [▼
                    "theme" => "netflix"
                ]
                }
        */
        // on redirige l'utilisateur sur une route donné : default_page
        return $this->redirectToRoute("model_default_page");
    }
}