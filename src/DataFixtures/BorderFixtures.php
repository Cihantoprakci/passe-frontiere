<?php

namespace App\DataFixtures;

use App\Entity\Border;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BorderFixtures extends Fixture
{
    public const BORDERS = [
        ['Moillesulaz', 'moillesulaz', 'Gaillard', 'Thônex', 'Route de Genève', 46.19105, 6.21180, 46.19310, 6.20250, 'moillesulaz.webp'],
        ['Thônex-Vallard', 'thonex-vallard', 'Gaillard', 'Thônex', 'Autoroute Blanche', 46.18600, 6.20853, 46.19084, 6.19477, null],
        ['Veyrier', 'veyrier', 'Étrembières', 'Veyrier', 'Route du Pas-de-l\'Échelle', 46.16450, 6.19300, 46.16700, 6.18500, null],
        ['La Croix-de-Rozon', 'croix-de-rozon', 'Collonges-sous-Salève', 'Bardonnex', 'Route du Pont-de-la-Fin', 46.14150, 6.14150, 46.14450, 6.13650, null],
        ['Bardonnex', 'bardonnex', 'Saint-Julien-en-Genevois', 'Bardonnex', 'Autoroute A41 / A1', 46.14370, 6.09715, 46.15464, 6.09933, 'bardonnex.webp'],
        ['Perly', 'perly', 'Saint-Julien-en-Genevois', 'Perly-Certoux', 'Route de Saint-Julien', 46.14900, 6.08700, 46.15350, 6.09150, null],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::BORDERS as $data) {
            $border = new Border();
            $border->setName($data[0]);
            $border->setSlug($data[1]);
            $border->setFrenchSide($data[2]);
            $border->setSwissSide($data[3]);
            $border->setRoad($data[4]);
            $border->setStartLat($data[5]);
            $border->setStartLng($data[6]);
            $border->setEndLat($data[7]);
            $border->setEndLng($data[8]);
            $border->setPhoto($data[9]);

            $manager->persist($border);
            $this->addReference('border-' . $data[1], $border);
        }

        $manager->flush();
    }
}
