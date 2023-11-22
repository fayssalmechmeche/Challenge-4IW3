<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231119044819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE formule_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE product_formula_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE formula_product (formula_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(formula_id, product_id))');
        $this->addSql('CREATE INDEX IDX_CC9DB811A50A6386 ON formula_product (formula_id)');
        $this->addSql('CREATE INDEX IDX_CC9DB8114584665A ON formula_product (product_id)');
        $this->addSql('CREATE TABLE product_formula (id INT NOT NULL, product_id INT NOT NULL, formula_id INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6E843314584665A ON product_formula (product_id)');
        $this->addSql('CREATE INDEX IDX_B6E84331A50A6386 ON product_formula (formula_id)');
        $this->addSql('ALTER TABLE formula_product ADD CONSTRAINT FK_CC9DB811A50A6386 FOREIGN KEY (formula_id) REFERENCES formula (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formula_product ADD CONSTRAINT FK_CC9DB8114584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_formula ADD CONSTRAINT FK_B6E843314584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_formula ADD CONSTRAINT FK_B6E84331A50A6386 FOREIGN KEY (formula_id) REFERENCES formula (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE formule');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE product_formula_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE formule_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE formule (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE formula_product DROP CONSTRAINT FK_CC9DB811A50A6386');
        $this->addSql('ALTER TABLE formula_product DROP CONSTRAINT FK_CC9DB8114584665A');
        $this->addSql('ALTER TABLE product_formula DROP CONSTRAINT FK_B6E843314584665A');
        $this->addSql('ALTER TABLE product_formula DROP CONSTRAINT FK_B6E84331A50A6386');
        $this->addSql('DROP TABLE formula_product');
        $this->addSql('DROP TABLE product_formula');
    }
}
