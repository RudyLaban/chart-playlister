<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201221211832 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chart (id INT AUTO_INCREMENT NOT NULL, chart_site_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_E5562A2ABB6D4C32 (chart_site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chart_site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chart_song (id INT AUTO_INCREMENT NOT NULL, chart_id INT NOT NULL, song_id INT NOT NULL, position INT DEFAULT NULL, INDEX IDX_610EB3EEBEF83E0A (chart_id), INDEX IDX_610EB3EEA0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, chart_id INT NOT NULL, streaming_site_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, INDEX IDX_D782112DBEF83E0A (chart_id), INDEX IDX_D782112DDC827D1 (streaming_site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song (id INT AUTO_INCREMENT NOT NULL, artist_id INT NOT NULL, name VARCHAR(255) NOT NULL, date DATETIME DEFAULT NULL, INDEX IDX_33EDEEA1B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE streaming_site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chart ADD CONSTRAINT FK_E5562A2ABB6D4C32 FOREIGN KEY (chart_site_id) REFERENCES chart_site (id)');
        $this->addSql('ALTER TABLE chart_song ADD CONSTRAINT FK_610EB3EEBEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id)');
        $this->addSql('ALTER TABLE chart_song ADD CONSTRAINT FK_610EB3EEA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112DBEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id)');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112DDC827D1 FOREIGN KEY (streaming_site_id) REFERENCES streaming_site (id)');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA1B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA1B7970CF8');
        $this->addSql('ALTER TABLE chart_song DROP FOREIGN KEY FK_610EB3EEBEF83E0A');
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112DBEF83E0A');
        $this->addSql('ALTER TABLE chart DROP FOREIGN KEY FK_E5562A2ABB6D4C32');
        $this->addSql('ALTER TABLE chart_song DROP FOREIGN KEY FK_610EB3EEA0BDB2F3');
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112DDC827D1');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE chart');
        $this->addSql('DROP TABLE chart_site');
        $this->addSql('DROP TABLE chart_song');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE song');
        $this->addSql('DROP TABLE streaming_site');
    }
}
