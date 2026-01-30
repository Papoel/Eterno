<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228190018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invitations (id INT AUTO_INCREMENT NOT NULL, friend_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', accepted TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_232710AE6A5458E8 (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lights` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_account_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', birthday_at DATE DEFAULT NULL, deceased_at DATE DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) DEFAULT NULL, username VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_38BCB2E83C0C9956 (user_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `messages` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', user_account_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', light_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', content LONGTEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DB021E963C0C9956 (user_account_id), INDEX IDX_DB021E963DA64B2C (light_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `users` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', birthday DATE DEFAULT NULL, mobile VARCHAR(10) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) DEFAULT NULL, username VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invitations ADD CONSTRAINT FK_232710AE6A5458E8 FOREIGN KEY (friend_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE `lights` ADD CONSTRAINT FK_38BCB2E83C0C9956 FOREIGN KEY (user_account_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE `messages` ADD CONSTRAINT FK_DB021E963C0C9956 FOREIGN KEY (user_account_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE `messages` ADD CONSTRAINT FK_DB021E963DA64B2C FOREIGN KEY (light_id) REFERENCES `lights` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitations DROP FOREIGN KEY FK_232710AE6A5458E8');
        $this->addSql('ALTER TABLE `lights` DROP FOREIGN KEY FK_38BCB2E83C0C9956');
        $this->addSql('ALTER TABLE `messages` DROP FOREIGN KEY FK_DB021E963C0C9956');
        $this->addSql('ALTER TABLE `messages` DROP FOREIGN KEY FK_DB021E963DA64B2C');
        $this->addSql('DROP TABLE invitations');
        $this->addSql('DROP TABLE `lights`');
        $this->addSql('DROP TABLE `messages`');
        $this->addSql('DROP TABLE `users`');
    }
}
