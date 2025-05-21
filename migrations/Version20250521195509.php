<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521195509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE question_theme (id SERIAL NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A79EF60C979B1AD6 ON question_theme (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question_theme ADD CONSTRAINT FK_A79EF60C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 DROP thematique
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question_theme DROP CONSTRAINT FK_A79EF60C979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE question_theme
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 ADD thematique VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
