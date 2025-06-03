<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602221122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE _tmp_user_import_1
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX compid_unique
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX compid_unique ON web_user (company_internal_id, company_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE _tmp_user_import_1 (email VARCHAR(255) NOT NULL, last_name VARCHAR(64) NOT NULL, first_name VARCHAR(64) NOT NULL, company_internal_id VARCHAR(255) NOT NULL, roles TEXT NOT NULL, job VARCHAR(128) DEFAULT NULL, team VARCHAR(128) DEFAULT NULL, PRIMARY KEY(email))
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX compid_unique
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX compid_unique ON web_user (company_internal_id, company_id)
        SQL);
    }
}
