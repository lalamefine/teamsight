<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518105114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE company_config (company_id INT NOT NULL, agt_id_type VARCHAR(32) DEFAULT 'email' NOT NULL, agt_auth_type VARCHAR(32) DEFAULT 'email-pass' NOT NULL, use_team_grouping BOOLEAN DEFAULT true NOT NULL, use_comp_ref BOOLEAN DEFAULT true NOT NULL, account_system VARCHAR(32) DEFAULT 'WebUI' NOT NULL, quest_fdb360 BOOLEAN DEFAULT true NOT NULL, quest_comp BOOLEAN DEFAULT true NOT NULL, quest_ea BOOLEAN DEFAULT true NOT NULL, quest_perc BOOLEAN DEFAULT true NOT NULL, use_account_dyn_camp BOOLEAN DEFAULT true NOT NULL, use_account_dyn_pan BOOLEAN DEFAULT true NOT NULL, data_retention INT DEFAULT 36 NOT NULL, PRIMARY KEY(company_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE obs_profile (id SERIAL NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, anonymous BOOLEAN DEFAULT false NOT NULL, can_validate_report BOOLEAN DEFAULT false NOT NULL, editable BOOLEAN DEFAULT true NOT NULL, can_see_validated_reports BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_24AA6C54979B1AD6 ON obs_profile (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config ADD CONSTRAINT FK_E9154B09979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE obs_profile ADD CONSTRAINT FK_24AA6C54979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_config DROP CONSTRAINT FK_E9154B09979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE obs_profile DROP CONSTRAINT FK_24AA6C54979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company_config
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE obs_profile
        SQL);
    }
}
