<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602214839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD job VARCHAR(128) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD team VARCHAR(128) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP metier
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP equipe
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD metier VARCHAR(128) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ADD equipe VARCHAR(128) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP job
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user DROP team
        SQL);
    }
}
