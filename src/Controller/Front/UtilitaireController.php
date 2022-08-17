<?php

namespace App\Controller\Front;

use App\Models\MoviesModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UtilitaireController extends AbstractController
{
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
        return $this->redirectToRoute("app_movie");
    }
}
