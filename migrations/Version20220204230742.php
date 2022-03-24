<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220204230742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_de7b2f9818f45c82');
        $this->addSql('CREATE INDEX IDX_DE7B2F9818F45C82 ON aff_tabdotwb (website_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_DE7B2F9818F45C82');
        $this->addSql('CREATE UNIQUE INDEX uniq_de7b2f9818f45c82 ON aff_tabdotwb (website_id)');
    }
}
