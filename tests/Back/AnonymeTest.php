<?php

namespace App\Tests\Back;

use App\Tests\WebTest\CustomWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AnonymeTest extends CustomWebTestCase
{
    /**
     * Tester les route interdite à un user
     * 
     * avec l'annotation dataProvider et un nom de function
     * cela permet de remplir la valeur de $url automatiquement 
     * suivant les valeurs retournés par cette function
     * 
     * @dataProvider getUrls
     */
    public function testBackPost403($url): void
    {
        // je créer mon client HTTP
        $client = static::createClient();
        
        // TODO : on récupère la route
        $crawler = $client->request('POST', $url);

        // TODO : on teste que la réponse de redirection vers la page de login
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // TODO : test sur l'URL de redirection
        // @link https://stackoverflow.com/questions/11047305/how-to-get-the-current-url-after-a-redirect-in-a-symfony2-webtestcase
        // $this->assertRegExp('/\/login$/', $client->getResponse()->headers->get('location'));
    }

    public function getUrls()
    {
        // yield c'est un return, MAIS la prochaine fois que l'on apelle cette function
        // on reprends là où on en était
        // exemple : 
        // 1. getUrls() => /back/movie/
        // 2. getUrls() => /back/casting/
        // 3. getUrls() => /back/user/
        yield ['/back/actor/new'];
        //? penser à avoir des fixtures spécifique pour la BDD de test pour que les ID fonctionne
        yield ['/back/actor/1/edit'];
        yield ['/back/actor/1'];

        yield ['/back/casting/new'];
        yield ['/back/casting/1/edit'];
        yield ['/back/casting/1'];

        yield ['/back/movie/new'];
        yield ['/back/movie/1/edit'];
        yield ['/back/movie/1'];

        yield ['/back/season/new'];
        yield ['/back/season/1/edit'];
        yield ['/back/season/1'];

        yield ['/back/user/new'];
        yield ['/back/user/1/edit'];
        yield ['/back/user/1'];

        // ajouter d'autre URL si besoin
    }


}
