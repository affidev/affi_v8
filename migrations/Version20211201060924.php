<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201060924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aff_tabdate (id SERIAL NOT NULL, tabdatestr TEXT NOT NULL, tabdatejso JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE aff_appointments ADD tabdate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_appointments ADD CONSTRAINT FK_5027549CE00596A0 FOREIGN KEY (tabdate_id) REFERENCES aff_tabdate (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5027549CE00596A0 ON aff_appointments (tabdate_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE aff_appointments DROP CONSTRAINT FK_5027549CE00596A0');
        $this->addSql('DROP TABLE aff_tabdate');
        $this->addSql('DROP INDEX UNIQ_5027549CE00596A0');
        $this->addSql('ALTER TABLE aff_appointments DROP tabdate_id');
    }
}
