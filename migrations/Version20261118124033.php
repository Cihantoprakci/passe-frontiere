<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261118124033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les 6 douanes du Genevois';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT IGNORE INTO border (name, slug, french_side, swiss_side, road, start_lat, start_lng, end_lat, end_lng, photo, active) VALUES
            ('Moillesulaz', 'moillesulaz', 'Gaillard', 'Thônex', 'Route de Genève', 46.19105, 6.21180, 46.19310, 6.20250, 'moillesulaz.webp', 1),
            ('Thônex-Vallard', 'thonex-vallard', 'Gaillard', 'Thônex', 'Autoroute Blanche', 46.18600, 6.20853, 46.19084, 6.19477, NULL, 1),
            ('Veyrier', 'veyrier', 'Étrembières', 'Veyrier', 'Route du Pas-de-l''Échelle', 46.16450, 6.19300, 46.16700, 6.18500, NULL, 1),
            ('La Croix-de-Rozon', 'croix-de-rozon', 'Collonges-sous-Salève', 'Bardonnex', 'Route du Pont-de-la-Fin', 46.14150, 6.14150, 46.14450, 6.13650, NULL, 1),
            ('Bardonnex', 'bardonnex', 'Saint-Julien-en-Genevois', 'Bardonnex', 'Autoroute A41 / A1', 46.14370, 6.09715, 46.15464, 6.09933, 'bardonnex.webp', 1),
            ('Perly', 'perly', 'Saint-Julien-en-Genevois', 'Perly-Certoux', 'Route de Saint-Julien', 46.14900, 6.08700, 46.15350, 6.09150, NULL, 1)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM border WHERE slug IN ('moillesulaz', 'thonex-vallard', 'veyrier', 'croix-de-rozon', 'bardonnex', 'perly')");
    }
}
