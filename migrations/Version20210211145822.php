<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210211145822 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Nomination yes vote count and no vote count';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nomination ADD yes_count INT NOT NULL');
        $this->addSql('ALTER TABLE nomination ADD no_count INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nomination DROP yes_count');
        $this->addSql('ALTER TABLE nomination DROP no_count');
    }
}
