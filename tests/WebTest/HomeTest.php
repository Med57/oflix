<?php

namespace App\Tests\WebTest;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HomeTest extends CustomWebTestCase
{
    public function testHome(): void
    {
        // On crée un client HTTP (le même/similaire que dans notre service OmdbApi)
        $client = static::createClient();

        // on demande la route '/' en GET
        $crawler = $client->request('GET', '/');
        //dump($crawler);

        // code 200
        $this->assertResponseIsSuccessful();
        // je vérifie que j'ai un h1 avec le texte dedans
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');

        
    }

    public function testAddReview()
    {
        // On crée un client HTTP (le même/similaire que dans notre service OmdbApi)
        $client = static::createClient();

        // on demande la route '/movie/4442/review' en GET
        //? ici on se pose la question de la route qui change à chaque d:f:l car les ID change
        //? on pense à changer la route pour avoir un slug à la place d'un ID, mais les titres sont aléatoire
        //? on pense à ajouter un film via E.M. avant le test, pour être certain que l'ID existe
        //? Benoit : on pense à aller chercher un film via le repository

        $movieRepository = static::getContainer()->get(MovieRepository::class);

        /** @var array $randomMovie */
        $randomMovie = $movieRepository->findRandomMovie();
        // dump($randomMovie);
        $crawler = $client->request('GET', '/movie/' .$randomMovie['id']. '/review');
        // debug on vérifie que l'URL est bien généré
        $this->assertResponseIsSuccessful();

        // @link https://symfony.com/doc/5.4/testing.html#submitting-forms
        // select the button
        $buttonCrawlerNode = $crawler->selectButton('review[sauvegarder]');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['review[username]'] = 'Fabien';
        $form['review[email]'] = 'Fabien@symfony.com';
        $form['review[content]'] = 'Que du bien sur ce film aléatoire';
        //? l'IDE ne sais pas où il habite ???
        // $form['review[rating]']->select(5);
        $form['review[rating]'] = 5;
        $form['review[reactions]'] = ["cry", "smile"];
        $form['review[watchedAt]'] = '2022-04-25';

        // submit the Form object
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // TODO : faire des tests sur la contrainte de validation

        $crawler = $client->request('GET', '/movie/' .$randomMovie['id']. '/review');
        // debug on vérifie que l'URL est bien généré
        $this->assertResponseIsSuccessful();

        // @link https://symfony.com/doc/5.4/testing.html#submitting-forms
        // select the button
        $buttonCrawlerNode = $crawler->selectButton('review[sauvegarder]');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['review[username]'] = 'a';

        // submit the Form object
        $client->submit($form);

        // si je n'ai pas réussi à valider les contrainte, je doit recevoir un 422
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    
}
