<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210308162247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add updated_at to vote (even though we don\'t really need it) so that Timestampable works';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE vote ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE vote DROP updated_at');
    }
}
