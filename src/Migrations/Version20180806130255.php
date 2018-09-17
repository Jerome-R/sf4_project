<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180806130255 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE symfony.app_users (id INT NOT NULL, full_name VARCHAR(255) DEFAULT NULL, username VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, token VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, credential_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, language VARCHAR(255) DEFAULT \'en\', PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EB1F91FF85E0677 ON symfony.app_users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EB1F91FE7927C74 ON symfony.app_users (email)');
        $this->addSql('COMMENT ON COLUMN symfony.app_users.roles IS \'(DC2Type:json)\'');
        $this->addSql('CREATE TABLE symfony.app_clients (id INT NOT NULL, id_client VARCHAR(50) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE symfony.app_users');
        $this->addSql('DROP TABLE symfony.app_clients');
    }
}
