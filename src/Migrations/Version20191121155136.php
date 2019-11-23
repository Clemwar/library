<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121155136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE author ADD birth_date DATE NOT NULL, ADD death_date DATE DEFAULT NULL, DROP birthdate, DROP deathdate, CHANGE firstname first_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE book CHANGE nbpages nb_pages INT DEFAULT NULL, CHANGE instock in_stock TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE author ADD deathdate DATE DEFAULT NULL, DROP birth_date, CHANGE first_name firstname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE death_date birthdate DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE book CHANGE nb_pages nbpages INT DEFAULT NULL, CHANGE in_stock instock TINYINT(1) NOT NULL');
    }
}
