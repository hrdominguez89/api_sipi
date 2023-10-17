<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231017005514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE computers (id INT AUTO_INCREMENT NOT NULL, status_computer_id INT NOT NULL, name VARCHAR(20) NOT NULL, brand VARCHAR(20) NOT NULL, model VARCHAR(30) NOT NULL, serie VARCHAR(50) NOT NULL, details LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_24195171A0FCA0E9 (status_computer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, version VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, observations LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programs_computers (id INT AUTO_INCREMENT NOT NULL, program_id INT NOT NULL, computer_id INT NOT NULL, INDEX IDX_F33910C83EB8070A (program_id), INDEX IDX_F33910C8A426D518 (computer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requests (id INT AUTO_INCREMENT NOT NULL, status_request_id INT NOT NULL, professor_id INT DEFAULT NULL, requested_programs LONGTEXT DEFAULT NULL, requested_amount INT NOT NULL, requested_date DATE NOT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_7B85D651432D5C23 (status_request_id), INDEX IDX_7B85D6517D2D84D5 (professor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requests_computers (id INT AUTO_INCREMENT NOT NULL, request_id INT NOT NULL, computer_id INT NOT NULL, student_id INT NOT NULL, returnet_at DATETIME DEFAULT NULL, observations LONGTEXT DEFAULT NULL, INDEX IDX_ADFA2AC6427EB8A5 (request_id), INDEX IDX_ADFA2AC6A426D518 (computer_id), INDEX IDX_ADFA2AC6CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_computer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_request (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE students (id INT AUTO_INCREMENT NOT NULL, dni INT NOT NULL, fullname VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, rol_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6494BAB96C (rol_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE computers ADD CONSTRAINT FK_24195171A0FCA0E9 FOREIGN KEY (status_computer_id) REFERENCES status_computer (id)');
        $this->addSql('ALTER TABLE programs_computers ADD CONSTRAINT FK_F33910C83EB8070A FOREIGN KEY (program_id) REFERENCES programs (id)');
        $this->addSql('ALTER TABLE programs_computers ADD CONSTRAINT FK_F33910C8A426D518 FOREIGN KEY (computer_id) REFERENCES computers (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651432D5C23 FOREIGN KEY (status_request_id) REFERENCES status_request (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D6517D2D84D5 FOREIGN KEY (professor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE requests_computers ADD CONSTRAINT FK_ADFA2AC6427EB8A5 FOREIGN KEY (request_id) REFERENCES requests (id)');
        $this->addSql('ALTER TABLE requests_computers ADD CONSTRAINT FK_ADFA2AC6A426D518 FOREIGN KEY (computer_id) REFERENCES computers (id)');
        $this->addSql('ALTER TABLE requests_computers ADD CONSTRAINT FK_ADFA2AC6CB944F1A FOREIGN KEY (student_id) REFERENCES students (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494BAB96C FOREIGN KEY (rol_id) REFERENCES roles (id)');
        $this->addSql('INSERT INTO roles (name) values ("ROLE_ADMIN")');
        $this->addSql('INSERT INTO roles (name) values ("ROLE_PROFESSOR")');
        $this->addSql('INSERT INTO roles (name) values ("ROLE_BEDEL")');
        $this->addSql('INSERT INTO status_computer (name) values ("DISABLED")');
        $this->addSql('INSERT INTO status_computer (name) values ("AVAILABLE")');
        $this->addSql('INSERT INTO status_computer (name) values ("NOT AVAILABLE")');
        $this->addSql('INSERT INTO status_request (name) values ("PENDING")');
        $this->addSql('INSERT INTO status_request (name) values ("ACCEPTED")');
        $this->addSql('INSERT INTO status_request (name) values ("REJECTED")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE computers DROP FOREIGN KEY FK_24195171A0FCA0E9');
        $this->addSql('ALTER TABLE programs_computers DROP FOREIGN KEY FK_F33910C83EB8070A');
        $this->addSql('ALTER TABLE programs_computers DROP FOREIGN KEY FK_F33910C8A426D518');
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D651432D5C23');
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D6517D2D84D5');
        $this->addSql('ALTER TABLE requests_computers DROP FOREIGN KEY FK_ADFA2AC6427EB8A5');
        $this->addSql('ALTER TABLE requests_computers DROP FOREIGN KEY FK_ADFA2AC6A426D518');
        $this->addSql('ALTER TABLE requests_computers DROP FOREIGN KEY FK_ADFA2AC6CB944F1A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494BAB96C');
        $this->addSql('DROP TABLE computers');
        $this->addSql('DROP TABLE programs');
        $this->addSql('DROP TABLE programs_computers');
        $this->addSql('DROP TABLE requests');
        $this->addSql('DROP TABLE requests_computers');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE status_computer');
        $this->addSql('DROP TABLE status_request');
        $this->addSql('DROP TABLE students');
        $this->addSql('DROP TABLE user');
    }
}
