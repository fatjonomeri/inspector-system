<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260214190230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jobs (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(50) NOT NULL, scheduled_date DATETIME DEFAULT NULL, completed_at DATETIME DEFAULT NULL, assessment LONGTEXT DEFAULT NULL, location VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, assigned_to_id INT DEFAULT NULL, INDEX IDX_A8936DC5F4BD7827 (assigned_to_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC5F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY FK_A8936DC5F4BD7827');
        $this->addSql('DROP TABLE jobs');
    }
}
