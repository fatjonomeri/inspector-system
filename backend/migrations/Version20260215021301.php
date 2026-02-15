<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215021301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE locations (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(10) NOT NULL, timezone VARCHAR(50) NOT NULL, country_code VARCHAR(2) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_17E64ABA5E237E06 (name), UNIQUE INDEX UNIQ_17E64ABA77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE jobs ADD location_id INT NOT NULL, DROP location');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC564D218E FOREIGN KEY (location_id) REFERENCES locations (id) ON DELETE RESTRICT');
        $this->addSql('CREATE INDEX IDX_A8936DC564D218E ON jobs (location_id)');
        $this->addSql('ALTER TABLE users ADD location_id INT NOT NULL, DROP location');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E964D218E FOREIGN KEY (location_id) REFERENCES locations (id) ON DELETE RESTRICT');
        $this->addSql('CREATE INDEX IDX_1483A5E964D218E ON users (location_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE locations');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY FK_A8936DC564D218E');
        $this->addSql('DROP INDEX IDX_A8936DC564D218E ON jobs');
        $this->addSql('ALTER TABLE jobs ADD location VARCHAR(100) NOT NULL, DROP location_id');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E964D218E');
        $this->addSql('DROP INDEX IDX_1483A5E964D218E ON users');
        $this->addSql('ALTER TABLE users ADD location VARCHAR(50) NOT NULL, DROP location_id');
    }
}
