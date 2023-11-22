<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117010726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT fk_8b27c52be6389d24');
        $this->addSql('DROP INDEX idx_8b27c52be6389d24');
        $this->addSql('ALTER TABLE devis DROP society_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE devis ADD society_id INT NOT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT fk_8b27c52be6389d24 FOREIGN KEY (society_id) REFERENCES society (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8b27c52be6389d24 ON devis (society_id)');
    }
}
