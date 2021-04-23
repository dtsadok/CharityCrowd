<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210426180157 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove Balance table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('DROP SEQUENCE balance_id_seq CASCADE');
        $this->addSql('DROP TABLE balance');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE balance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE balance (id INT NOT NULL, amount_cents BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
    }
}
