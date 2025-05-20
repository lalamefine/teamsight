<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520225215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 ADD template_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 ADD parent_question_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 ADD CONSTRAINT FK_ECF108D5DA0FB8 FOREIGN KEY (template_id) REFERENCES template360 (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 ADD CONSTRAINT FK_ECF108D750BE4CF FOREIGN KEY (parent_question_id) REFERENCES question360 (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ECF108D5DA0FB8 ON question360 (template_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_ECF108D750BE4CF ON question360 (parent_question_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 DROP CONSTRAINT FK_ECF108D5DA0FB8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 DROP CONSTRAINT FK_ECF108D750BE4CF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_ECF108D5DA0FB8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_ECF108D750BE4CF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 DROP template_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 DROP parent_question_id
        SQL);
    }
}
