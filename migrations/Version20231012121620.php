<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012121620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE requests_computers (id INT AUTO_INCREMENT NOT NULL, request_id INT NOT NULL, computer_id INT NOT NULL, student_id INT NOT NULL, returnet_at DATETIME DEFAULT NULL, observations LONGTEXT DEFAULT NULL, INDEX IDX_ADFA2AC6427EB8A5 (request_id), INDEX IDX_ADFA2AC6A426D518 (computer_id), INDEX IDX_ADFA2AC6CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE requests_computers ADD CONSTRAINT FK_ADFA2AC6427EB8A5 FOREIGN KEY (request_id) REFERENCES requests (id)');
        $this->addSql('ALTER TABLE requests_computers ADD CONSTRAINT FK_ADFA2AC6A426D518 FOREIGN KEY (computer_id) REFERENCES computers (id)');
        $this->addSql('ALTER TABLE requests_computers ADD CONSTRAINT FK_ADFA2AC6CB944F1A FOREIGN KEY (student_id) REFERENCES students (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE requests_computers DROP FOREIGN KEY FK_ADFA2AC6427EB8A5');
        $this->addSql('ALTER TABLE requests_computers DROP FOREIGN KEY FK_ADFA2AC6A426D518');
        $this->addSql('ALTER TABLE requests_computers DROP FOREIGN KEY FK_ADFA2AC6CB944F1A');
        $this->addSql('DROP TABLE requests_computers');
    }
}
