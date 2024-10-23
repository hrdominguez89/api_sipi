<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022180231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE status_request SET name="Pendiente" where name ="PENDING"');
        $this->addSql('UPDATE status_request SET name="Aceptado" where name ="ACCEPTED"');
        $this->addSql('UPDATE status_request SET name="Rechazado" where name ="REJECTED"');
        
        $this->addSql('UPDATE status_computer SET name="Deshabilitado" where name ="DISABLED"');
        $this->addSql('UPDATE status_computer SET name="Disponible" where name ="AVAILABLE"');
        $this->addSql('UPDATE status_computer SET name="No disponible" where name ="NOT AVAILABLE"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        
        $this->addSql('UPDATE status_request SET name="PENDING" where name ="Pendiente"');
        $this->addSql('UPDATE status_request SET name="ACCEPTED" where name ="Aceptado"');
        $this->addSql('UPDATE status_request SET name="REJECTED" where name ="Rechazado"');
        
        $this->addSql('UPDATE status_computer SET name="DISABLED" where name="Deshabilitado"');
        $this->addSql('UPDATE status_computer SET name="AVAILABLE" where name="Disponible"');
        $this->addSql('UPDATE status_computer SET name="NOT AVAILABLE" where name="No disponible"');
    }
}
