<?php

namespace App\Service;

use App\Entity\BorderReading;
use App\Repository\BorderRepository;
use Doctrine\ORM\EntityManagerInterface;

class BorderReadingRecorder
{
    public function __construct(
        private BorderRepository $borderRepository,
        private TomTomClient $tomTomClient,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function recordAll(): int
    {
        $count = 0;
        $now = new \DateTimeImmutable();

        foreach ($this->borderRepository->findActive() as $border) {
            $times = $this->tomTomClient->getTravelTimes($border);
            if (!$times) {
                continue;
            }

            $reading = new BorderReading();
            $reading->setBorder($border);
            $reading->setMeasuredAt($now);
            $reading->setTravelSeconds($times['travel']);
            $reading->setFreeFlowSeconds($times['freeFlow']);
            $reading->setDelaySeconds(max(0, $times['travel'] - $times['freeFlow']));

            $this->entityManager->persist($reading);
            $count++;
        }

        $this->entityManager->flush();

        return $count;
    }
}
