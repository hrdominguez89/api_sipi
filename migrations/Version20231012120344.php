<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012120344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programs_computers ADD computer_id INT NOT NULL');
        $this->addSql('ALTER TABLE programs_computers ADD CONSTRAINT FK_F33910C8A426D518 FOREIGN KEY (computer_id) REFERENCES computers (id)');
        $this->addSql('CREATE INDEX IDX_F33910C8A426D518 ON programs_computers (computer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programs_computers DROP FOREIGN KEY FK_F33910C8A426D518');
        $this->addSql('DROP INDEX IDX_F33910C8A426D518 ON programs_computers');
        $this->addSql('ALTER TABLE programs_computers DROP computer_id');
    }
}
