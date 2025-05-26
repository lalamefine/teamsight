<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525231248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE campaign_feedback360 (id SERIAL NOT NULL, company_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, panel_proposal_opened_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, panel_proposal_closed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, begin_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, report_validation_deadline TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, current_state VARCHAR(16) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1A6B724F979B1AD6 ON campaign_feedback360 (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.panel_proposal_opened_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.panel_proposal_closed_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.begin_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.end_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.report_validation_deadline IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 ADD CONSTRAINT FK_1A6B724F979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config DROP account_system
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER first_name SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER last_name SET NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 DROP CONSTRAINT FK_1A6B724F979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE campaign_feedback360
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config ADD account_system VARCHAR(32) DEFAULT 'WebUI' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER first_name DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE web_user ALTER last_name DROP NOT NULL
        SQL);
    }
}
