<?php

namespace App\DataFixtures;

use App\Entity\Border;
use App\Entity\BorderReading;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReadingFixtures extends Fixture implements DependentFixtureInterface
{
    private const RUSH = [5 => 1, 6 => 3, 7 => 9, 8 => 14, 9 => 11, 10 => 6, 11 => 4, 12 => 3, 13 => 3, 14 => 4, 15 => 5, 16 => 9, 17 => 13, 18 => 10, 19 => 5, 20 => 2, 21 => 1];

    private const BUSY = ['moillesulaz' => 1.0, 'thonex-vallard' => 0.7, 'veyrier' => 0.3, 'croix-de-rozon' => 0.2, 'bardonnex' => 1.2, 'perly' => 0.5];

    public function load(ObjectManager $manager): void
    {
        $start = new \DateTimeImmutable('-5 weeks midnight');
        $now = new \DateTimeImmutable();

        foreach (self::BUSY as $slug => $factor) {
            $border = $this->getReference('border-' . $slug, Border::class);
            $date = $start;
            $count = 0;

            while ($date < $now) {
                $hour = (int) $date->format('G');
                $weekend = (int) $date->format('N') >= 6;
                $minutes = self::RUSH[$hour] ?? 0;

                if ($weekend) {
                    $minutes = $minutes / 3;
                }

                $delay = (int) round(($minutes * $factor + mt_rand(0, 20) / 10) * 60);
                $freeFlow = 120;

                $reading = new BorderReading();
                $reading->setBorder($border);
                $reading->setMeasuredAt($date);
                $reading->setFreeFlowSeconds($freeFlow);
                $reading->setTravelSeconds($freeFlow + $delay);
                $reading->setDelaySeconds($delay);
                $manager->persist($reading);

                $count++;
                if ($count % 500 === 0) {
                    $manager->flush();
                }

                $date = $date->modify('+15 minutes');
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BorderFixtures::class];
    }
}
