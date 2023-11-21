<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231119045406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formula_product DROP CONSTRAINT fk_cc9db811a50a6386');
        $this->addSql('ALTER TABLE formula_product DROP CONSTRAINT fk_cc9db8114584665a');
        $this->addSql('DROP TABLE formula_product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE formula_product (formula_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(formula_id, product_id))');
        $this->addSql('CREATE INDEX idx_cc9db8114584665a ON formula_product (product_id)');
        $this->addSql('CREATE INDEX idx_cc9db811a50a6386 ON formula_product (formula_id)');
        $this->addSql('ALTER TABLE formula_product ADD CONSTRAINT fk_cc9db811a50a6386 FOREIGN KEY (formula_id) REFERENCES formula (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formula_product ADD CONSTRAINT fk_cc9db8114584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
