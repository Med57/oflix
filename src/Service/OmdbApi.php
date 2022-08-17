<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OmdbApi
{
    // j'ai tout copié d'ici : 
    // https://symfony.com/doc/current/http_client.html#basic-usage
    private $client;
    private $params;
    /**
     * J'ai besoin de HTTPClient, j'utilise ton l'injection au constructeur
     * Comme ça dès que je demande cette classe, l'injection de HTTPClient seras faites en auto
     *
     * @param HttpClientInterface $client
     * @param ParameterBagInterface $params Nous permet de récupèrer la valeurs dans le fichier services.yaml
     */ 
    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->params = $params;
    }

    public function fetch(string $titre): array
    {
        // https://www.omdbapi.com/?t=Totoro&apikey=a93b767b
        $response = $this->client->request(
            'GET',
            'https://www.omdbapi.com/', [
                // https://symfony.com/doc/current/http_client.html#query-string-parameters
                // these values are automatically encoded before including them in the URL
                'query' => [
                    't' => $titre, // urlencode() sera exécute automatiquement
                    //'apikey' => 'a93b767b', // on a déporté la clé dans le fichier services.yaml
                    'apikey' => $this->params->get('app.omdbapi.key')
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        /* $content = '{
            "Title": "Totoro",
            "Poster": "https://m.media-amazon.com/images/M/MV5BYzJjMTYyMjQtZDI0My00ZjE2LTkyNGYtOTllNGQxNDMyZjE0XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg",'
            */
        // json_decode ??? ==> plus tard quand on fera notre API
        $content = $response->toArray();
        // $content = ['Title' => "Totoro", 'Poster' => 'https://m.media-amazon.com/images/M', ...]

        return $content;
    }

    /**
     * parce que la flème de refaire ce code à chaque fois
     *
     * @param string $title le titre du film
     * @return string l'url du poster
     */
    public function fetchPoster(string $title): string
    {
        $arrayOmdbApi = $this->fetch($title);

        if (array_key_exists("Poster",$arrayOmdbApi)){
            return $arrayOmdbApi["Poster"];
        } else {
            //return "https://picsum.photos/id/".rand(100, 1000)."/200/300";
            // Merci Benoit :)
            return "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg";
        }
    }
}