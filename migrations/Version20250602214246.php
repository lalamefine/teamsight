<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602214246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config ADD fdb360ask_panel_to_evalue BOOLEAN DEFAULT false NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config ADD fdb360ask_panel_to_hierarchy BOOLEAN DEFAULT false NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD metier VARCHAR(128) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD equipe VARCHAR(128) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP metier
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP equipe
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config DROP fdb360ask_panel_to_evalue
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config DROP fdb360ask_panel_to_hierarchy
        SQL);
    }
}
