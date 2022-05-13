<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510214512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subproceso (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, subcategoria_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, directorio VARCHAR(255) NOT NULL, INDEX IDX_D97EEC163397707A (categoria_id), INDEX IDX_D97EEC1688D3B71A (subcategoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subproceso ADD CONSTRAINT FK_D97EEC163397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE subproceso ADD CONSTRAINT FK_D97EEC1688D3B71A FOREIGN KEY (subcategoria_id) REFERENCES subcategoria (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE subproceso');
    }
}
