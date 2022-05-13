<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220512024714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE registro (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, subcategoria_id INT NOT NULL, subproceso_id INT NOT NULL, periodo_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_397CA85B3397707A (categoria_id), INDEX IDX_397CA85B88D3B71A (subcategoria_id), INDEX IDX_397CA85B3B9CFAF0 (subproceso_id), INDEX IDX_397CA85B9C3921AB (periodo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B88D3B71A FOREIGN KEY (subcategoria_id) REFERENCES subcategoria (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B3B9CFAF0 FOREIGN KEY (subproceso_id) REFERENCES subproceso (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B9C3921AB FOREIGN KEY (periodo_id) REFERENCES periodo (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE registro');
    }
}
