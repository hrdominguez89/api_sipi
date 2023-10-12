<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012115602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE programs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, version VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, observations LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requests (id INT AUTO_INCREMENT NOT NULL, professor_id INT NOT NULL, status_request_id INT NOT NULL, requested_programs LONGTEXT DEFAULT NULL, requested_amount INT NOT NULL, requested_date DATE NOT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_7B85D6517D2D84D5 (professor_id), INDEX IDX_7B85D651432D5C23 (status_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D6517D2D84D5 FOREIGN KEY (professor_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651432D5C23 FOREIGN KEY (status_request_id) REFERENCES status_request (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D6517D2D84D5');
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D651432D5C23');
        $this->addSql('DROP TABLE programs');
        $this->addSql('DROP TABLE requests');
    }
}
