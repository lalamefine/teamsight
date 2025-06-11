<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609103155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 ADD panel_proposal_hierarchy_closed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 RENAME COLUMN panel_proposal_closed_at TO panel_proposal_evalue_closed_at
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.panel_proposal_hierarchy_closed_at IS '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 ADD panel_proposal_closed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 DROP panel_proposal_evalue_closed_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 DROP panel_proposal_hierarchy_closed_at
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN campaign_feedback360.panel_proposal_closed_at IS '(DC2Type:datetime_immutable)'
        SQL);
    }
}
