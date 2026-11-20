<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120111839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE code_postal (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(5) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CC94AC3777153098 ON code_postal (code)');
        $this->addSql('CREATE TABLE commune (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code_insee VARCHAR(5) DEFAULT NULL, nom VARCHAR(255) NOT NULL, population INTEGER DEFAULT NULL, surface DOUBLE PRECISION DEFAULT NULL, departement_id INTEGER NOT NULL, CONSTRAINT FK_E2E2D1EECCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E2E2D1EE1649A761 ON commune (code_insee)');
        $this->addSql('CREATE INDEX IDX_E2E2D1EECCF9E01E ON commune (departement_id)');
        $this->addSql('CREATE TABLE commune_code_postal (commune_id INTEGER NOT NULL, code_postal_id INTEGER NOT NULL, PRIMARY KEY (commune_id, code_postal_id), CONSTRAINT FK_DA8B8D65131A4F72 FOREIGN KEY (commune_id) REFERENCES commune (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DA8B8D65B2B59251 FOREIGN KEY (code_postal_id) REFERENCES code_postal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DA8B8D65131A4F72 ON commune_code_postal (commune_id)');
        $this->addSql('CREATE INDEX IDX_DA8B8D65B2B59251 ON commune_code_postal (code_postal_id)');
        $this->addSql('CREATE TABLE departement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(3) NOT NULL, nom VARCHAR(255) NOT NULL, region_id INTEGER NOT NULL, CONSTRAINT FK_C1765B6398260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1765B6377153098 ON departement (code)');
        $this->addSql('CREATE INDEX IDX_C1765B6398260155 ON departement (region_id)');
        $this->addSql('CREATE TABLE region (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(3) NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F17677153098 ON region (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE code_postal');
        $this->addSql('DROP TABLE commune');
        $this->addSql('DROP TABLE commune_code_postal');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE region');
    }
}
