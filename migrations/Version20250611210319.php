<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611210319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE answer (id SERIAL NOT NULL, question_id INT NOT NULL, observation_id INT NOT NULL, by_id INT NOT NULL, value DOUBLE PRECISION DEFAULT NULL, comment TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DADD4A251E27F6BF ON answer (question_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DADD4A251409DD88 ON answer (observation_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DADD4A25AAE72004 ON answer (by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE observation360 (id SERIAL NOT NULL, agent_id INT NOT NULL, campaign_id INT DEFAULT NULL, state VARCHAR(32) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4E13BBA3414710B ON observation360 (agent_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4E13BBAF639F774 ON observation360 (campaign_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE observer (id SERIAL NOT NULL, observation_id INT NOT NULL, agent_id INT NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9B6F44E71409DD88 ON observer (observation_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9B6F44E73414710B ON observer (agent_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN observer.started_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN observer.finished_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question360 (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251409DD88 FOREIGN KEY (observation_id) REFERENCES observation360 (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25AAE72004 FOREIGN KEY (by_id) REFERENCES observer (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observation360 ADD CONSTRAINT FK_4E13BBA3414710B FOREIGN KEY (agent_id) REFERENCES web_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observation360 ADD CONSTRAINT FK_4E13BBAF639F774 FOREIGN KEY (campaign_id) REFERENCES campaign_feedback360 (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer ADD CONSTRAINT FK_9B6F44E71409DD88 FOREIGN KEY (observation_id) REFERENCES observation360 (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer ADD CONSTRAINT FK_9B6F44E73414710B FOREIGN KEY (agent_id) REFERENCES web_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 ALTER current_state TYPE VARCHAR(32)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1532E98A5E237E06BF1CD3C3979B1AD6 ON template360 (name, version, company_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE answer DROP CONSTRAINT FK_DADD4A251E27F6BF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE answer DROP CONSTRAINT FK_DADD4A251409DD88
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE answer DROP CONSTRAINT FK_DADD4A25AAE72004
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observation360 DROP CONSTRAINT FK_4E13BBA3414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observation360 DROP CONSTRAINT FK_4E13BBAF639F774
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer DROP CONSTRAINT FK_9B6F44E71409DD88
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE observer DROP CONSTRAINT FK_9B6F44E73414710B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE answer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE observation360
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE observer
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1532E98A5E237E06BF1CD3C3979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE campaign_feedback360 ALTER current_state TYPE VARCHAR(16)
        SQL);
    }
}
