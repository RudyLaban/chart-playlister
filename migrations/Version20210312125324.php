<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210312125324 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playlist ADD chart_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112DBEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id)');
        $this->addSql('CREATE INDEX IDX_D782112DBEF83E0A ON playlist (chart_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112DBEF83E0A');
        $this->addSql('DROP INDEX IDX_D782112DBEF83E0A ON playlist');
        $this->addSql('ALTER TABLE playlist DROP chart_id');
    }
}
