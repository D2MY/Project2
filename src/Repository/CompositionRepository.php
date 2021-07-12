<?php

namespace App\Repository;

use App\Entity\Composition;
use App\Entity\Fandom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Composition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Composition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Composition[]    findAll()
 * @method Composition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Composition::class);
    }

    public function getUserCompositionsWithFandoms(int $id)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.title, c.description, f.name')
            ->leftJoin(Fandom::class,'f', 'WITH', 'f.id = c.fandom')
            ->where('c.user = :id')
            ->setParameter('id', $id)
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery();
    }

    public function lastUpdated(int $limit) :array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.title, c.description, f.name')
            ->leftJoin(Fandom::class,'f', 'WITH', 'f.id = c.fandom')
            ->orderBy('c.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return CompositionService[] Returns an array of CompositionService objects
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
    public function findOneBySomeField($value): ?CompositionService
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
