<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301212640 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add password and roles to member, make sure nickname is unique';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE member ADD roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78A188FE64 ON member (nickname)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_70E4FA78A188FE64');
        $this->addSql('ALTER TABLE member DROP password');
        $this->addSql('ALTER TABLE member DROP roles');
    }
}
