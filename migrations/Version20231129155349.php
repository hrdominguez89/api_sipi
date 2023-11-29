<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129155349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE computers CHANGE visible visible TINYINT(1) DEFAULT 1');
        $this->addSql('UPDATE computers set visible = true');
        $this->addSql('ALTER TABLE programs CHANGE visible visible TINYINT(1) DEFAULT 1');
        $this->addSql('UPDATE programs set visible = true');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE computers CHANGE visible visible TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE programs CHANGE visible visible TINYINT(1) DEFAULT NULL');
    }
}
