<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529103207 extends AbstractMigration
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
            ALTER TABLE web_user ALTER can_connect SET DEFAULT true
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER displayed SET DEFAULT true
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER email_validated SET DEFAULT false
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE _tmp_user_import_1 (id VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles TEXT NOT NULL, team VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER can_connect DROP DEFAULT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER displayed DROP DEFAULT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER email_validated DROP DEFAULT
        SQL);
    }
}
