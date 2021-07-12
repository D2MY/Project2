<?php

namespace App\Repository;

use App\Entity\Chapter;
use App\Entity\Appraisal;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Appraisal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appraisal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appraisal[]    findAll()
 * @method Appraisal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appraisal::class);
    }

    public function getUserLikeForChapter(User $user, Chapter $chapter)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->where('l.user = :user AND l.chapter = :chapter')
            ->setParameter('user', $user)
            ->setParameter('chapter', $chapter)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Appraisal[] Returns an array of Appraisal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Appraisal
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
