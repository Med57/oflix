<?php

namespace App\Repository;

use App\Entity\Casting;
use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casting::class);
    }

    /**
     * Retourne la liste des casting d'un film trié par creditOrder
     *
     * @param integer $id L'id du film
     * @return Casting[]  liste des castings du film
     */
    public function findByMovieOrderedByCreditOrder(Movie $movie): array
    {
        //? puisque je suis dans CastingRepo, je fais des requetes sur la table Casting

        // l'alias va nous servir à faire référence à la table dans les paramètres de construction de la query
        $queryBuilder = $this->createQueryBuilder('castingEntity'); // FROM casting castingEntity

        // on paramètre le ORDER BY
        $queryBuilder = $queryBuilder->orderBy("castingEntity.creditOrder"); // ORDER BY creditOrder

        // on fait comme un prépare, on construit le where ET ensuite on donne la valeur
        $queryBuilder = $queryBuilder->andWhere('castingEntity.movie = :movie'); // WHERE movie_id = $id
        $queryBuilder = $queryBuilder->setParameter('movie', $movie);

        // on demande la construction de la requete
        $query = $queryBuilder->getQuery(); // SELECT * ....

        // on execute la requete
        $result = $query->getResult(); 
        
        return $result;

        /* en une seule ligne
        return $this->createQueryBuilder('c')
            ->orderBy("c.creditOrder")
            ->andWhere('c.movie = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
        */   
    }

    /**
     * Retourne la liste des casting d'un film trié par creditOrder
     * 
     * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/dql-doctrine-query-language.html
     * 
     * @param integer $id L'id du film
     * @return Casting[]  liste des castings du film
     */
    public function findByMovieOrderedByCreditOrderDQL(Movie $movie): array
    {
        
        // Pour faire du DQL, il faut demander EntityManager
        $em = $this->getEntityManager();
        /*
            SELECT castingEntity
            SELECT de l'entité avec l'alias 'castingEntity' avec toutes ses propriétés

            FROM App\Entity\Casting castingEntity
            FROM la table de l'entité App\Entity\Casting auquel je donne un Alias 'castingEntity'

            WHERE castingEntity.movie = :movie
            WHERE l'entité avec l'alias 'castingEntity' avec la propriété 'movie' 
            qui est égale à la valeur de l'objet $movie (paramètre de la function)

        */
        $query = $em->createQuery("SELECT castingEntity 
            FROM App\Entity\Casting castingEntity 
            WHERE castingEntity.movie = :movie
            ORDER BY castingEntity.creditOrder ASC");
        $query = $query->setParameter("movie", $movie);
        return $query->getResult();
    }

    /**
     * Get Casting + Person for a given movie
     */
    public function findAllJoinedToPersonByMovieQb(Movie $movie)
    {
        // SELECT c.* FROM casting
        return $this->createQueryBuilder('c')
            // JOIN casting ON c.person = person.id
            ->innerJoin('c.actor', 'a')
            // SELECT aussi a.* :)
            ->addSelect('a')
            // WHERE c.movie_id = movie.id
            ->where('c.movie = :movie')
            ->setParameter('movie', $movie)
            // ORDER BY creditOrder ASC
            ->orderBy('c.creditOrder', 'ASC')
            // Récupère la requête
            ->getQuery()
            // Récupère les résultats
            ->getResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Casting $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Casting $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Casting[] Returns an array of Casting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Casting
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
