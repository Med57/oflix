<?php

namespace App\Tests\Back;

use App\Repository\UserRepository;
use App\Tests\WebTest\CustomWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends CustomWebTestCase
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
    public function testSomething($url): void
    {
        /*
         Objectifs tester les droits d'acces

         en tant que user@user.com je n'ai pas le droit d'aller sur la route '/back/movie'
        */

        // je créer mon client HTTP
        $client = static::createClient();

        // TODO : récupérer un User
        // un user est une entité comme une autre
        // il me faut le UserRepository
        // je récupère le service vie la container
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        // je cherche mon utilisateur par son e-mail (unique) parce que j'ai pas son ID
        $user = $userRepository->findOneBy(["email" => 'user@user.com']);

        // TODO : se connecter
        $client->loginUser($user);

        // TODO : on récupère la route
        $crawler = $client->request('GET', $url);

        // TODO : on teste que la réponse = Response::HTTP_FORBIDDEN (403)
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        /* SI J'AI UN COMPTE ADMIN EN BDD 

        $admin = $userRepository->findOneBy(["email" => "admin@admin.com"]);

        $client->loginUser($admin);

        $crawler = $client->request('GET', '/back/movie');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        */
    }

    public function getUrls()
    {
        // yield c'est un return, MAIS la prochaine fois que l'on apelle cette function
        // on reprends là où on en était
        // exemple : 
        // 1. getUrls() => /back/movie/
        // 2. getUrls() => /back/casting/
        // 3. getUrls() => /back/user/
        yield ['/back/movie/'];
        yield ['/back/casting/'];
        yield ['/back/user/'];

        // ajouter d'autre URL si besoin
    }


}
