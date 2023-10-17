<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231017030720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user MODIFY fullname VARCHAR(50) NOT NULL, MODIFY created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, MODIFY active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('INSERT INTO user (fullname,password,email,rol_id,roles) VALUES ("Administrador","$2y$13$6knEQnxfP3Aw.Rl0K1iO3eurO9d8BRS/clzqZ8TyMUOnrydJHx6NW","admin@admin.com",1,\'["ROLE_USER"]\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP fullname, DROP created_at, DROP active');
    }
}
