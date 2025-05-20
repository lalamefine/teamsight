<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520210521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE question360 (id SERIAL NOT NULL, libelle VARCHAR(255) NOT NULL, custom_responses JSON DEFAULT NULL, verbatim BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE question360_obs_profile (question360_id INT NOT NULL, obs_profile_id INT NOT NULL, PRIMARY KEY(question360_id, obs_profile_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6BFFFAD1CEB5809D ON question360_obs_profile (question360_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6BFFFAD1F2BF2D25 ON question360_obs_profile (obs_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360_obs_profile ADD CONSTRAINT FK_6BFFFAD1CEB5809D FOREIGN KEY (question360_id) REFERENCES question360 (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360_obs_profile ADD CONSTRAINT FK_6BFFFAD1F2BF2D25 FOREIGN KEY (obs_profile_id) REFERENCES obs_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE template360 ALTER responses TYPE JSON USING responses::json
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN template360.responses IS NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360_obs_profile DROP CONSTRAINT FK_6BFFFAD1CEB5809D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE question360_obs_profile DROP CONSTRAINT FK_6BFFFAD1F2BF2D25
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE question360
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE question360_obs_profile
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE template360 ALTER responses TYPE TEXT
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN template360.responses IS '(DC2Type:array)'
        SQL);
    }
}
