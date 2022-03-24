<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119074506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aff_dispatch ADD analityc_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_dispatch ADD CONSTRAINT FK_E261F9D033636C54 FOREIGN KEY (analityc_id) REFERENCES aff_taganalytic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E261F9D033636C54 ON aff_dispatch (analityc_id)');
        $this->addSql('ALTER TABLE aff_taganalytic ADD dispatch_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_taganalytic ADD tabgps JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_taganalytic ADD tabcat JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_taganalytic ADD tablikewebsite JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_taganalytic ADD sessions JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_taganalytic DROP keywebsite');
        $this->addSql('ALTER TABLE aff_taganalytic DROP create_at');
        $this->addSql('ALTER TABLE aff_taganalytic RENAME COLUMN tagname TO log');
        $this->addSql('ALTER TABLE aff_taganalytic ADD CONSTRAINT FK_8DC52567C774D3B9 FOREIGN KEY (dispatch_id) REFERENCES aff_dispatch (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8DC52567C774D3B9 ON aff_taganalytic (dispatch_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE aff_taganalytic DROP CONSTRAINT FK_8DC52567C774D3B9');
        $this->addSql('DROP INDEX UNIQ_8DC52567C774D3B9');
        $this->addSql('ALTER TABLE aff_taganalytic ADD keywebsite VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE aff_taganalytic ADD create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE aff_taganalytic DROP dispatch_id');
        $this->addSql('ALTER TABLE aff_taganalytic DROP tabgps');
        $this->addSql('ALTER TABLE aff_taganalytic DROP tabcat');
        $this->addSql('ALTER TABLE aff_taganalytic DROP tablikewebsite');
        $this->addSql('ALTER TABLE aff_taganalytic DROP sessions');
        $this->addSql('ALTER TABLE aff_taganalytic RENAME COLUMN log TO tagname');
        $this->addSql('ALTER TABLE aff_dispatch DROP CONSTRAINT FK_E261F9D033636C54');
        $this->addSql('DROP INDEX UNIQ_E261F9D033636C54');
        $this->addSql('ALTER TABLE aff_dispatch DROP analityc_id');
    }
}
