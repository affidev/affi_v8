<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119071545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aff_hits (id SERIAL NOT NULL, website_id INT DEFAULT NULL, gps_id INT DEFAULT NULL, publi INT DEFAULT 0 NOT NULL, "like" INT DEFAULT 0 NOT NULL, lastdayshow TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE2001F918F45C82 ON aff_hits (website_id)');
        $this->addSql('CREATE INDEX IDX_CE2001F9BD6B6DDE ON aff_hits (gps_id)');
        $this->addSql('CREATE TABLE hits_tagcat (hits_id INT NOT NULL, tagcat_id INT NOT NULL, PRIMARY KEY(hits_id, tagcat_id))');
        $this->addSql('CREATE INDEX IDX_56030F5E616D631 ON hits_tagcat (hits_id)');
        $this->addSql('CREATE INDEX IDX_56030F54E795CD9 ON hits_tagcat (tagcat_id)');
        $this->addSql('CREATE TABLE aff_tagcat (id SERIAL NOT NULL, score INT DEFAULT 0 NOT NULL, ponderation DOUBLE PRECISION DEFAULT \'0\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tagcat_hits (tagcat_id INT NOT NULL, hits_id INT NOT NULL, PRIMARY KEY(tagcat_id, hits_id))');
        $this->addSql('CREATE INDEX IDX_41C5574D4E795CD9 ON tagcat_hits (tagcat_id)');
        $this->addSql('CREATE INDEX IDX_41C5574DE616D631 ON tagcat_hits (hits_id)');
        $this->addSql('ALTER TABLE aff_hits ADD CONSTRAINT FK_CE2001F918F45C82 FOREIGN KEY (website_id) REFERENCES aff_websites (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE aff_hits ADD CONSTRAINT FK_CE2001F9BD6B6DDE FOREIGN KEY (gps_id) REFERENCES aff_gps (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hits_tagcat ADD CONSTRAINT FK_56030F5E616D631 FOREIGN KEY (hits_id) REFERENCES aff_hits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hits_tagcat ADD CONSTRAINT FK_56030F54E795CD9 FOREIGN KEY (tagcat_id) REFERENCES aff_tagcat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tagcat_hits ADD CONSTRAINT FK_41C5574D4E795CD9 FOREIGN KEY (tagcat_id) REFERENCES aff_tagcat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tagcat_hits ADD CONSTRAINT FK_41C5574DE616D631 FOREIGN KEY (hits_id) REFERENCES aff_hits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE aff_taguery ADD catag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_taguery ADD CONSTRAINT FK_53EB9D741D97FDD2 FOREIGN KEY (catag_id) REFERENCES aff_tagcat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_53EB9D741D97FDD2 ON aff_taguery (catag_id)');
        $this->addSql('ALTER TABLE aff_websites ADD hits_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aff_websites ADD CONSTRAINT FK_41544807E616D631 FOREIGN KEY (hits_id) REFERENCES aff_hits (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41544807B8242929 ON aff_websites (namewebsite)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41544807E616D631 ON aff_websites (hits_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hits_tagcat DROP CONSTRAINT FK_56030F5E616D631');
        $this->addSql('ALTER TABLE tagcat_hits DROP CONSTRAINT FK_41C5574DE616D631');
        $this->addSql('ALTER TABLE aff_websites DROP CONSTRAINT FK_41544807E616D631');
        $this->addSql('ALTER TABLE hits_tagcat DROP CONSTRAINT FK_56030F54E795CD9');
        $this->addSql('ALTER TABLE tagcat_hits DROP CONSTRAINT FK_41C5574D4E795CD9');
        $this->addSql('ALTER TABLE aff_taguery DROP CONSTRAINT FK_53EB9D741D97FDD2');
        $this->addSql('DROP TABLE aff_hits');
        $this->addSql('DROP TABLE hits_tagcat');
        $this->addSql('DROP TABLE aff_tagcat');
        $this->addSql('DROP TABLE tagcat_hits');
        $this->addSql('DROP INDEX IDX_53EB9D741D97FDD2');
        $this->addSql('ALTER TABLE aff_taguery DROP catag_id');
        $this->addSql('DROP INDEX UNIQ_41544807B8242929');
        $this->addSql('DROP INDEX UNIQ_41544807E616D631');
        $this->addSql('ALTER TABLE aff_websites DROP hits_id');
    }
}
