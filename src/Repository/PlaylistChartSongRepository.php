<?php

namespace App\Repository;

use App\Entity\PlaylistChartSong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlaylistChartSong|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaylistChartSong|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaylistChartSong[]    findAll()
 * @method PlaylistChartSong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistChartSongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaylistChartSong::class);
    }

    // /**
    //  * @return PlaylistChartSong[] Returns an array of PlaylistChartSong objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlaylistChartSong
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
