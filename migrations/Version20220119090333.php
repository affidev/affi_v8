<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119090333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE hits_tagcat');
        $this->addSql('DROP TABLE tagcat_hits');
        $this->addSql('ALTER TABLE aff_tagcat ADD hits_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_tagcat ADD CONSTRAINT FK_C2C08B5EE616D631 FOREIGN KEY (hits_id) REFERENCES aff_hits (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C2C08B5EE616D631 ON aff_tagcat (hits_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE hits_tagcat (hits_id INT NOT NULL, tagcat_id INT NOT NULL, PRIMARY KEY(hits_id, tagcat_id))');
        $this->addSql('CREATE INDEX idx_56030f54e795cd9 ON hits_tagcat (tagcat_id)');
        $this->addSql('CREATE INDEX idx_56030f5e616d631 ON hits_tagcat (hits_id)');
        $this->addSql('CREATE TABLE tagcat_hits (tagcat_id INT NOT NULL, hits_id INT NOT NULL, PRIMARY KEY(tagcat_id, hits_id))');
        $this->addSql('CREATE INDEX idx_41c5574de616d631 ON tagcat_hits (hits_id)');
        $this->addSql('CREATE INDEX idx_41c5574d4e795cd9 ON tagcat_hits (tagcat_id)');
        $this->addSql('ALTER TABLE hits_tagcat ADD CONSTRAINT fk_56030f5e616d631 FOREIGN KEY (hits_id) REFERENCES aff_hits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hits_tagcat ADD CONSTRAINT fk_56030f54e795cd9 FOREIGN KEY (tagcat_id) REFERENCES aff_tagcat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tagcat_hits ADD CONSTRAINT fk_41c5574d4e795cd9 FOREIGN KEY (tagcat_id) REFERENCES aff_tagcat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tagcat_hits ADD CONSTRAINT fk_41c5574de616d631 FOREIGN KEY (hits_id) REFERENCES aff_hits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE aff_tagcat DROP CONSTRAINT FK_C2C08B5EE616D631');
        $this->addSql('DROP INDEX IDX_C2C08B5EE616D631');
        $this->addSql('ALTER TABLE aff_tagcat DROP hits_id');
    }
}
