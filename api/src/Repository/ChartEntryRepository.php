<?php

namespace App\Repository;

use App\Entity\Chart;
use App\Entity\ChartEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChartEntry>
 */
class ChartEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartEntry::class);
    }

    /**
     * Trouve les entries d'une chart, avec filtre optionnel par date
     *
     * @param Chart $chart
     * @param \DateTimeImmutable|null $snapshotDate
     * @param int $limit
     * @return ChartEntry[]
     */
    public function findByChartAndDate(Chart $chart, ?\DateTimeImmutable $snapshotDate = null, int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.chart = :chart')
            ->setParameter('chart', $chart)
            ->orderBy('e.position', 'ASC')
            ->setMaxResults($limit);

        if ($snapshotDate) {
            $qb->andWhere('e.snapshotDate = :date')
               ->setParameter('date', $snapshotDate);
        }

        return $qb->getQuery()->getResult();
    }
}
