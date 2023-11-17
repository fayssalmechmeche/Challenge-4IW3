<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117130828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04adbe6903fd');
        $this->addSql('DROP INDEX idx_d34a04adbe6903fd');
        $this->addSql('ALTER TABLE product ADD product_category VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product DROP product_category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product ADD product_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product DROP product_category');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04adbe6903fd FOREIGN KEY (product_category_id) REFERENCES product_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d34a04adbe6903fd ON product (product_category_id)');
    }
}
