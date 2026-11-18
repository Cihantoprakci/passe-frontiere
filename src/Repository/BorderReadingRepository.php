<?php

namespace App\Repository;

use App\Entity\Border;
use App\Entity\BorderReading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BorderReadingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BorderReading::class);
    }

    public function findLatest(Border $border): ?BorderReading
    {
        return $this->findOneBy(['border' => $border], ['measuredAt' => 'DESC']);
    }

    public function findLatestForAllBorders(): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.border', 'b')
            ->addSelect('b')
            ->where('b.active = true')
            ->andWhere('r.measuredAt = (SELECT MAX(r2.measuredAt) FROM App\Entity\BorderReading r2 WHERE r2.border = r.border)')
            ->orderBy('r.delaySeconds', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAverageDelayByHour(Border $border, int $isoDay, int $weeks = 5): array
    {
        $sql = 'SELECT HOUR(measured_at) AS hour, ROUND(AVG(delay_seconds) / 60) AS minutes
            FROM border_reading
            WHERE border_id = :border
            AND DAYOFWEEK(measured_at) = :day
            AND measured_at >= :since
            GROUP BY HOUR(measured_at)
            ORDER BY hour';

        $rows = $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'border' => $border->getId(),
            'day' => $isoDay % 7 + 1,
            'since' => (new \DateTimeImmutable('-' . $weeks . ' weeks'))->format('Y-m-d H:i:s'),
        ])->fetchAllAssociative();

        $hours = [];
        foreach ($rows as $row) {
            $hours[(int) $row['hour']] = (int) $row['minutes'];
        }

        return $hours;
    }
}
