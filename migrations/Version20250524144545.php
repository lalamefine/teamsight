<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250524144545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD first_name VARCHAR(64) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD last_name VARCHAR(64) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP username
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD username VARCHAR(128) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP first_name
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP last_name
        SQL);
    }
}
