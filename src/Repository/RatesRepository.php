<?php

namespace App\Repository;

use App\Entity\Composition;
use App\Entity\Rates;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rates|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rates|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rates[]    findAll()
 * @method Rates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rates::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkRateByUserForComposition(User $user, Composition $composition)
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.user = :user AND r.composition = :composition')
            ->setParameter('user', $user)
            ->setParameter('composition', $composition)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRatesForComposition(Composition $composition): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.rate')
            ->where('r.composition = :composition')
            ->setParameter('composition', $composition)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getUserRateForComposition(User $user, Composition $composition)
    {
        return $this->createQueryBuilder('r')
            ->select('r.rate')
            ->where('r.user = :user AND r.composition = :composition')
            ->setParameter('user', $user)
            ->setParameter('composition', $composition)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Rates[] Returns an array of Rates objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rates
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
