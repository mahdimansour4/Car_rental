<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240715084636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fiche_maintenance (id INT AUTO_INCREMENT NOT NULL, voiture_id INT DEFAULT NULL, date DATETIME NOT NULL, type VARCHAR(255) NOT NULL, kilometrage INT NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_CF3C952181A8BA (voiture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE papier_voiture (id INT AUTO_INCREMENT NOT NULL, voiture_id INT DEFAULT NULL, date_fin_vignette DATETIME NOT NULL, prix_vignette DOUBLE PRECISION NOT NULL, date_fin_assurance VARCHAR(255) NOT NULL, prix_assurance DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_F6717B6C181A8BA (voiture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fiche_maintenance ADD CONSTRAINT FK_CF3C952181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE papier_voiture ADD CONSTRAINT FK_F6717B6C181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_maintenance DROP FOREIGN KEY FK_CF3C952181A8BA');
        $this->addSql('ALTER TABLE papier_voiture DROP FOREIGN KEY FK_F6717B6C181A8BA');
        $this->addSql('DROP TABLE fiche_maintenance');
        $this->addSql('DROP TABLE papier_voiture');
    }
}
