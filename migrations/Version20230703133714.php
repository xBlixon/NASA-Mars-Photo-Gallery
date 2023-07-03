<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230703133714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE holiday_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE holiday (id INT NOT NULL, name VARCHAR(128) NOT NULL, nationwide BOOLEAN NOT NULL, type VARCHAR(64) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE holiday_id_seq CASCADE');
        $this->addSql('DROP TABLE holiday');
    }
}
