<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611223719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 ADD base_template_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 ADD CONSTRAINT FK_1A6B724FCFFF940 FOREIGN KEY (base_template_id) REFERENCES template360 (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1A6B724FCFFF940 ON campaign_feedback360 (base_template_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer ADD profile_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer ADD CONSTRAINT FK_9B6F44E7CCFA12B8 FOREIGN KEY (profile_id) REFERENCES obs_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9B6F44E7CCFA12B8 ON observer (profile_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 DROP CONSTRAINT FK_1A6B724FCFFF940
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1A6B724FCFFF940
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 DROP base_template_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer DROP CONSTRAINT FK_9B6F44E7CCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9B6F44E7CCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer DROP profile_id
        SQL);
    }
}
