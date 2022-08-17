<?php

namespace App\Tests\Service;

use App\Service\OmdbApi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OmdbApiTest extends KernelTestCase
{
    public function testFetchPoster(): void
    {
        // on démarre le kernel de Symfony pour avoir accès aux services (Injection de dépendance)
        $kernel = self::bootKernel();

        // je veux tester cette function : $kernel->getEnvironment()
        // je prédit que le résultat sera : 'test'
        // si ma prédiction est bonne, la méthode assertSame répondra TRUE : la test est valide
        // sinon la méthode assertSame répondra FALSE : la test est invalide
        $this->assertSame('test', $kernel->getEnvironment());

        // On va utiliser les "dessous" de l'injection de dépendance
        // on demande au container de service de nous fournir un service donné via son FQCN
        // cela est strictement identique à l'injection de dépendance habituelle
        /** @var OmdbApi $omdbapi */
        $omdbapi = static::getContainer()->get(OmdbApi::class);

        $urlPoster = $omdbapi->fetchPoster("Totoro");
        // dump($urlPoster);
        // @link https://phpunit.readthedocs.io/fr/latest/assertions.html
        $this->assertStringStartsWith('https://', $urlPoster);

        $this->assertEquals(
            'https://m.media-amazon.com/images/M/MV5BYzJjMTYyMjQtZDI0My00ZjE2LTkyNGYtOTllNGQxNDMyZjE0XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg',
            $urlPoster
        );

        $urlPoster = $omdbapi->fetchPoster("TagadaTsoinTsoin");
        // je donne un film qui n'existe pas, j'ai donc l'image par défaut
        $this->assertEquals(
            "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg",
            $urlPoster
        );
    }
}
