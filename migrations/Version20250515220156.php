<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515220156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO company (id, name, slug, enabled_features) VALUES (1, 'Teamsight', 'teamsight', '[]');            
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO web_user (username, email, hpass, roles, can_connect, displayed, email_validated) VALUES ('Louis Triboulin', 'louis@triboulin.fr', '$2y$13$0oZvbmNnluSSNnf.Cxbc6eu/tZSrqnU8fxYzdJWtdlFki4b33JC3a', '["ROLE_SUPER_ADMIN"]', true, true, true);
        SQL); // pass is 1234
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
