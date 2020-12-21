<?php

namespace App\Repository;

use App\Entity\ChartSong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChartSong|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChartSong|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChartSong[]    findAll()
 * @method ChartSong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChartSongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartSong::class);
    }

    // /**
    //  * @return ChartSong[] Returns an array of ChartSong objects
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
    public function findOneBySomeField($value): ?ChartSong
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
