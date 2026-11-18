<?php

namespace App\Repository;

use App\Entity\Alert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.user', 'u')
            ->where('a.active = true')
            ->andWhere('u.alertsConsent = true')
            ->getQuery()
            ->getResult();
    }
}
