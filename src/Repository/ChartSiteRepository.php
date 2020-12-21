<?php

namespace App\Repository;

use App\Entity\ChartSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChartSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChartSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChartSite[]    findAll()
 * @method ChartSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChartSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartSite::class);
    }

    // /**
    //  * @return ChartSite[] Returns an array of ChartSite objects
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
    public function findOneBySomeField($value): ?ChartSite
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
