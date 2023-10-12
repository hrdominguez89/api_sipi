<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012120205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE computers (id INT AUTO_INCREMENT NOT NULL, status_computer_id INT NOT NULL, name VARCHAR(20) NOT NULL, brand VARCHAR(20) NOT NULL, model VARCHAR(30) NOT NULL, serie VARCHAR(50) NOT NULL, details LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_24195171A0FCA0E9 (status_computer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programs_computers (id INT AUTO_INCREMENT NOT NULL, program_id INT NOT NULL, INDEX IDX_F33910C83EB8070A (program_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE computers ADD CONSTRAINT FK_24195171A0FCA0E9 FOREIGN KEY (status_computer_id) REFERENCES status_computer (id)');
        $this->addSql('ALTER TABLE programs_computers ADD CONSTRAINT FK_F33910C83EB8070A FOREIGN KEY (program_id) REFERENCES programs (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE computers DROP FOREIGN KEY FK_24195171A0FCA0E9');
        $this->addSql('ALTER TABLE programs_computers DROP FOREIGN KEY FK_F33910C83EB8070A');
        $this->addSql('DROP TABLE computers');
        $this->addSql('DROP TABLE programs_computers');
    }
}
