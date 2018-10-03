<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181002133930 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE app_actions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_clients_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_actions (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(25) NOT NULL, type VARCHAR(25) NOT NULL, canal VARCHAR(15) NOT NULL, author VARCHAR(50) NOT NULL, edited_by VARCHAR(50) DEFAULT NULL, status VARCHAR(25) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modified_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E81571A277153098 ON app_actions (code)');
        $this->addSql('CREATE TABLE app_clients (id INT NOT NULL, id_client VARCHAR(50) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app_users (id INT NOT NULL, full_name VARCHAR(255) DEFAULT NULL, username VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, token VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, credential_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, language VARCHAR(255) DEFAULT \'en\', entity_manager VARCHAR(255) DEFAULT \'default\', PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824F85E0677 ON app_users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824E7927C74 ON app_users (email)');
        $this->addSql('COMMENT ON COLUMN app_users.roles IS \'(DC2Type:json)\'');
        $this->addSql('CREATE TABLE ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('COMMENT ON COLUMN ext_log_entries.data IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE app_actions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_clients_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_users_id_seq CASCADE');
        $this->addSql('DROP TABLE app_actions');
        $this->addSql('DROP TABLE app_clients');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}
