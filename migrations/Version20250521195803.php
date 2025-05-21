<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521195803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 ADD thematique_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 ADD CONSTRAINT FK_ECF108D476556AF FOREIGN KEY (thematique_id) REFERENCES question_theme (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ECF108D476556AF ON question360 (thematique_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 DROP CONSTRAINT FK_ECF108D476556AF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_ECF108D476556AF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360 DROP thematique_id
        SQL);
    }
}
