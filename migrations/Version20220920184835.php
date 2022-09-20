<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920184835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD hdd VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD ram VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD cpu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD battery VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD connectivity VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD screen_size VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD screen_resolution VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP price');
        $this->addSql('ALTER TABLE product DROP hdd');
        $this->addSql('ALTER TABLE product DROP ram');
        $this->addSql('ALTER TABLE product DROP cpu');
        $this->addSql('ALTER TABLE product DROP battery');
        $this->addSql('ALTER TABLE product DROP connectivity');
        $this->addSql('ALTER TABLE product DROP screen_size');
        $this->addSql('ALTER TABLE product DROP screen_resolution');
    }
}
