<?php

namespace App\Repository;

use App\Entity\StreamingSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StreamingSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method StreamingSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method StreamingSite[]    findAll()
 * @method StreamingSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StreamingSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StreamingSite::class);
    }

    // /**
    //  * @return StreamingSite[] Returns an array of StreamingSite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StreamingSite
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
