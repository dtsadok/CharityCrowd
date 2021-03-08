<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210308152118 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add percentage allocation to nomination';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE nomination ADD percentage INT NOT NULL DEFAULT 0');
        $this->addSql('CREATE INDEX percentage_idx ON nomination (percentage)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP INDEX percentage_idx');
        $this->addSql('ALTER TABLE nomination DROP percentage');
    }
}
