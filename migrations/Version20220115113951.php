<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220115113951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aff_adresses ADD gps_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_adresses ADD CONSTRAINT FK_8B6ABAD8BD6B6DDE FOREIGN KEY (gps_id) REFERENCES aff_gps (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8B6ABAD8BD6B6DDE ON aff_adresses (gps_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE aff_adresses DROP CONSTRAINT FK_8B6ABAD8BD6B6DDE');
        $this->addSql('DROP INDEX IDX_8B6ABAD8BD6B6DDE');
        $this->addSql('ALTER TABLE aff_adresses DROP gps_id');
    }
}
