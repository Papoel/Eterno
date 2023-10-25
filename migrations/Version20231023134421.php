<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023134421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initialisation de la base de données';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(sql: 'CREATE TABLE `lights` (
            id INT AUTO_INCREMENT NOT NULL, 
            firstname VARCHAR(50) NOT NULL, 
            lastname VARCHAR(50) DEFAULT NULL, 
            username VARCHAR(50) DEFAULT NULL, 
            birthday_at DATE DEFAULT NULL, 
            deceased_at DATE DEFAULT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            user_account_id INT NOT NULL, 
            INDEX IDX_38BCB2E83C0C9956 (user_account_id), 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(sql: 'CREATE TABLE `messages` (
            id INT AUTO_INCREMENT NOT NULL, 
            message LONGTEXT NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            user_account_id INT NOT NULL, 
            light_id INT NOT NULL, 
            INDEX IDX_DB021E963C0C9956 (user_account_id), 
            INDEX IDX_DB021E963DA64B2C (light_id), 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(sql: 'CREATE TABLE `users` (
            id INT AUTO_INCREMENT NOT NULL, 
            firstname VARCHAR(50) NOT NULL, 
            lastname VARCHAR(50) DEFAULT NULL, 
            username VARCHAR(50) DEFAULT NULL, 
            email VARCHAR(180) NOT NULL, 
            password VARCHAR(255) NOT NULL, 
            roles JSON NOT NULL COMMENT \'(DC2Type:json)\', 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(sql: 'ALTER TABLE `lights` ADD CONSTRAINT FK_38BCB2E83C0C9956 FOREIGN KEY (user_account_id) REFERENCES `users` (id)');
        $this->addSql(sql: 'ALTER TABLE `messages` ADD CONSTRAINT FK_DB021E963C0C9956 FOREIGN KEY (user_account_id) REFERENCES `users` (id)');
        $this->addSql(sql: 'ALTER TABLE `messages` ADD CONSTRAINT FK_DB021E963DA64B2C FOREIGN KEY (light_id) REFERENCES `lights` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(sql: 'ALTER TABLE `lights` DROP FOREIGN KEY FK_38BCB2E83C0C9956');
        $this->addSql(sql: 'ALTER TABLE `messages` DROP FOREIGN KEY FK_DB021E963C0C9956');
        $this->addSql(sql: 'ALTER TABLE `messages` DROP FOREIGN KEY FK_DB021E963DA64B2C');
        $this->addSql(sql: 'DROP TABLE `lights`');
        $this->addSql(sql: 'DROP TABLE `messages`');
        $this->addSql(sql: 'DROP TABLE `users`');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
