<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210426183015 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Change nomination % to float (0 to 1.00)';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE nomination ALTER percentage TYPE DOUBLE PRECISION');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE nomination ALTER percentage TYPE INT');
    }
}
