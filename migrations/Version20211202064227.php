<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202064227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresses_sectors (adresses_id INT NOT NULL, sectors_id INT NOT NULL, PRIMARY KEY(adresses_id, sectors_id))');
        $this->addSql('CREATE INDEX IDX_C507063885E14726 ON adresses_sectors (adresses_id)');
        $this->addSql('CREATE INDEX IDX_C50706383479DC16 ON adresses_sectors (sectors_id)');
        $this->addSql('ALTER TABLE adresses_sectors ADD CONSTRAINT FK_C507063885E14726 FOREIGN KEY (adresses_id) REFERENCES aff_adresses (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE adresses_sectors ADD CONSTRAINT FK_C50706383479DC16 FOREIGN KEY (sectors_id) REFERENCES aff_sector (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE aff_adresses DROP CONSTRAINT fk_8b6abad8de95c867');
        $this->addSql('DROP INDEX idx_8b6abad8de95c867');
        $this->addSql('ALTER TABLE aff_adresses DROP sector_id');
        $this->addSql('ALTER TABLE aff_postevent ADD sector_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_postevent ADD CONSTRAINT FK_A2D70416DE95C867 FOREIGN KEY (sector_id) REFERENCES aff_sector (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2D70416DE95C867 ON aff_postevent (sector_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE adresses_sectors');
        $this->addSql('ALTER TABLE aff_adresses ADD sector_id INT NOT NULL');
        $this->addSql('ALTER TABLE aff_adresses ADD CONSTRAINT fk_8b6abad8de95c867 FOREIGN KEY (sector_id) REFERENCES aff_sector (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8b6abad8de95c867 ON aff_adresses (sector_id)');
        $this->addSql('ALTER TABLE aff_postevent DROP CONSTRAINT FK_A2D70416DE95C867');
        $this->addSql('DROP INDEX UNIQ_A2D70416DE95C867');
        $this->addSql('ALTER TABLE aff_postevent DROP sector_id');
    }
}
