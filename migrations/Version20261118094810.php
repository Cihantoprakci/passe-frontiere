<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261118094810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alert (id INT AUTO_INCREMENT NOT NULL, threshold_minutes INT NOT NULL, window_start TIME NOT NULL, window_end TIME NOT NULL, active TINYINT NOT NULL, last_sent_at DATETIME DEFAULT NULL, user_id INT NOT NULL, border_id INT NOT NULL, INDEX IDX_17FD46C1A76ED395 (user_id), INDEX IDX_17FD46C17C210FDD (border_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE border (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, french_side VARCHAR(100) NOT NULL, swiss_side VARCHAR(100) NOT NULL, road VARCHAR(150) NOT NULL, start_lat DOUBLE PRECISION NOT NULL, start_lng DOUBLE PRECISION NOT NULL, end_lat DOUBLE PRECISION NOT NULL, end_lng DOUBLE PRECISION NOT NULL, photo VARCHAR(255) DEFAULT NULL, active TINYINT NOT NULL, UNIQUE INDEX UNIQ_C7F56B77989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE border_reading (id INT AUTO_INCREMENT NOT NULL, measured_at DATETIME NOT NULL, travel_seconds INT NOT NULL, free_flow_seconds INT NOT NULL, delay_seconds INT NOT NULL, border_id INT NOT NULL, INDEX IDX_841C21CB7C210FDDFF8CEB12 (border_id, measured_at), INDEX IDX_841C21CB7C210FDD (border_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commute (id INT AUTO_INCREMENT NOT NULL, station_from VARCHAR(100) NOT NULL, leave_at TIME NOT NULL, user_id INT NOT NULL, border_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_52CF2B35A76ED395 (user_id), INDEX IDX_52CF2B357C210FDD (border_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, alerts_consent TINYINT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C17C210FDD FOREIGN KEY (border_id) REFERENCES border (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE border_reading ADD CONSTRAINT FK_841C21CB7C210FDD FOREIGN KEY (border_id) REFERENCES border (id)');
        $this->addSql('ALTER TABLE commute ADD CONSTRAINT FK_52CF2B35A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commute ADD CONSTRAINT FK_52CF2B357C210FDD FOREIGN KEY (border_id) REFERENCES border (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C1A76ED395');
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C17C210FDD');
        $this->addSql('ALTER TABLE border_reading DROP FOREIGN KEY FK_841C21CB7C210FDD');
        $this->addSql('ALTER TABLE commute DROP FOREIGN KEY FK_52CF2B35A76ED395');
        $this->addSql('ALTER TABLE commute DROP FOREIGN KEY FK_52CF2B357C210FDD');
        $this->addSql('DROP TABLE alert');
        $this->addSql('DROP TABLE border');
        $this->addSql('DROP TABLE border_reading');
        $this->addSql('DROP TABLE commute');
        $this->addSql('DROP TABLE `user`');
    }
}
