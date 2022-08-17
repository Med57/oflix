<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /** 
     * function for list all movie in form
     */
    public function getAllMovieOrderByTitleForForm()
    {
        return $this->createQueryBuilder('movie')
                        ->orderBy('movie.title', 'ASC');
    }

    /**
     * FindOneBySlug
     *
     * @param string $slug
     */
    public function findBySlug(string $slug)
    {
        return $this->createQueryBuilder('m')
            ->where('m.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
    }

    /**
     * Renvoit les informations d'un film aléatoire sous forme de tableau associatif
     *
     * @return array
     */
    public function findRandomMovie(): array
    {
        // @link https://sql.sh/fonctions/rand
        /*
        select * 
            from movie
            ORDER BY RAND()
            LIMIT 1
        */
        // @link https://stackoverflow.com/questions/10762538/how-to-select-randomly-with-doctrine

        // on a accès à SQL, via DBAL, équivalant de PDO mais chez doctrine
        // @link https://symfony.com/doc/current/doctrine.html#querying-with-sql
        // On récupère la connection à la BDD avec l'objet DBAL
        $dbal = $this->getEntityManager()->getConnection();
        $sql = "select * 
            from movie
            ORDER BY RAND()
            LIMIT 1";
        
        $stmt = $dbal->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // on ne peut pas récup un objet Movie, 
        // mais twig nous permet de ne pas faire de différence entre un objet et un tableau
        return $resultSet->fetchAssociative();

        
        //version de Benoit, un bonne idée avec ses limites
        
        //$randomMovie = null;
        //while($randomMovie == null){
            // $rand = rand(10000);
            // puisque ça nous renvoit un null si pas trouvé
            // on boucle tant que on trouve null
            //$randomMovie = $this->find($rand);
        //}
        
    }

    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Movie $entity, bool $flush = true): void
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
    public function remove(Movie $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
