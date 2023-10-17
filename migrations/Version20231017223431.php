<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231017223431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "lights_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "messages_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "users_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "lights" (id INT NOT NULL, user_account_id INT NOT NULL, birthday_at DATE DEFAULT NULL, deceased_at DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) DEFAULT NULL, username VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_38BCB2E83C0C9956 ON "lights" (user_account_id)');
        $this->addSql('COMMENT ON COLUMN "lights".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "messages" (id INT NOT NULL, user_account_id INT NOT NULL, light_id INT NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB021E963C0C9956 ON "messages" (user_account_id)');
        $this->addSql('CREATE INDEX IDX_DB021E963DA64B2C ON "messages" (light_id)');
        $this->addSql('COMMENT ON COLUMN "messages".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "users" (id INT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) DEFAULT NULL, username VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON "users" (email)');
        $this->addSql('COMMENT ON COLUMN "users".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "lights" ADD CONSTRAINT FK_38BCB2E83C0C9956 FOREIGN KEY (user_account_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "messages" ADD CONSTRAINT FK_DB021E963C0C9956 FOREIGN KEY (user_account_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "messages" ADD CONSTRAINT FK_DB021E963DA64B2C FOREIGN KEY (light_id) REFERENCES "lights" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "lights_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "messages_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "users_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "lights" DROP CONSTRAINT FK_38BCB2E83C0C9956');
        $this->addSql('ALTER TABLE "messages" DROP CONSTRAINT FK_DB021E963C0C9956');
        $this->addSql('ALTER TABLE "messages" DROP CONSTRAINT FK_DB021E963DA64B2C');
        $this->addSql('DROP TABLE "lights"');
        $this->addSql('DROP TABLE "messages"');
        $this->addSql('DROP TABLE "users"');
    }
}
