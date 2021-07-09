<?php

namespace App\Repository;

use App\Entity\Composition;
use App\Entity\Fandom;
use App\Entity\Favourite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Favourite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favourite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favourite[]    findAll()
 * @method Favourite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favourite::class);
    }

    public function isFavouriteCompositionForUser(User $user, Composition $composition)
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->where('f.user = :user AND f.composition = :composition')
            ->setParameter('user', $user)
            ->setParameter('composition', $composition)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function favouritesForUser(User $user)
    {
        return $this->createQueryBuilder('f')
            ->select('f, c.title, c.description, fandom.name')
            ->leftJoin(Composition::class,'c', 'WITH', 'f.composition = c.id')
            ->leftJoin(Fandom::class,'fandom', 'WITH', 'fandom.id = c.fandom')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Favourite[] Returns an array of Favourite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Favourite
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
