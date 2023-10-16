<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016220413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables light, message et users';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE light_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "users_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE light (id INT NOT NULL, user_account_id INT NOT NULL, birthday_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deceased_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) DEFAULT NULL, username VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6B1A5CF73C0C9956 ON light (user_account_id)');
        $this->addSql('COMMENT ON COLUMN light.birthday_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN light.deceased_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN light.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, user_account_id INT NOT NULL, light_id INT NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F3C0C9956 ON message (user_account_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F3DA64B2C ON message (light_id)');
        $this->addSql('COMMENT ON COLUMN message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "users" (id INT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) DEFAULT NULL, username VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON "users" (email)');
        $this->addSql('COMMENT ON COLUMN "users".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE light ADD CONSTRAINT FK_6B1A5CF73C0C9956 FOREIGN KEY (user_account_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3C0C9956 FOREIGN KEY (user_account_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3DA64B2C FOREIGN KEY (light_id) REFERENCES light (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE light_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "users_id_seq" CASCADE');
        $this->addSql('ALTER TABLE light DROP CONSTRAINT FK_6B1A5CF73C0C9956');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F3C0C9956');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F3DA64B2C');
        $this->addSql('DROP TABLE light');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE "users"');
    }
}
