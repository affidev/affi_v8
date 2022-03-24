<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119094756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tagcat_taguery (tagcat_id INT NOT NULL, taguery_id INT NOT NULL, PRIMARY KEY(tagcat_id, taguery_id))');
        $this->addSql('CREATE INDEX IDX_AD3D27124E795CD9 ON tagcat_taguery (tagcat_id)');
        $this->addSql('CREATE INDEX IDX_AD3D27126752502F ON tagcat_taguery (taguery_id)');
        $this->addSql('ALTER TABLE tagcat_taguery ADD CONSTRAINT FK_AD3D27124E795CD9 FOREIGN KEY (tagcat_id) REFERENCES aff_tagcat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tagcat_taguery ADD CONSTRAINT FK_AD3D27126752502F FOREIGN KEY (taguery_id) REFERENCES aff_taguery (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE aff_taguery DROP CONSTRAINT fk_53eb9d741d97fdd2');
        $this->addSql('DROP INDEX idx_53eb9d741d97fdd2');
        $this->addSql('ALTER TABLE aff_taguery DROP catag_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE tagcat_taguery');
        $this->addSql('ALTER TABLE aff_taguery ADD catag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_taguery ADD CONSTRAINT fk_53eb9d741d97fdd2 FOREIGN KEY (catag_id) REFERENCES aff_tagcat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_53eb9d741d97fdd2 ON aff_taguery (catag_id)');
    }
}
