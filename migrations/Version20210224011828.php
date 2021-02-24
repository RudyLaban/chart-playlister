<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224011828 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE playlist_chart_song (id INT AUTO_INCREMENT NOT NULL, playlist_id INT NOT NULL, chart_song_id INT NOT NULL, url VARCHAR(255) DEFAULT NULL, external_id VARCHAR(255) NOT NULL, INDEX IDX_C35A3C5C6BBD148 (playlist_id), INDEX IDX_C35A3C5CED6DE887 (chart_song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playlist_chart_song ADD CONSTRAINT FK_C35A3C5C6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
        $this->addSql('ALTER TABLE playlist_chart_song ADD CONSTRAINT FK_C35A3C5CED6DE887 FOREIGN KEY (chart_song_id) REFERENCES chart_song (id)');
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112DBEF83E0A');
        $this->addSql('DROP INDEX IDX_D782112DBEF83E0A ON playlist');
        $this->addSql('ALTER TABLE playlist DROP chart_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE playlist_chart_song');
        $this->addSql('ALTER TABLE playlist ADD chart_id INT NOT NULL');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112DBEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id)');
        $this->addSql('CREATE INDEX IDX_D782112DBEF83E0A ON playlist (chart_id)');
    }
}
