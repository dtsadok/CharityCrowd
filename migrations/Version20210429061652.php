<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210429061652 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Invite Codes';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE invite_code_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE invite_code (id INT NOT NULL, code VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP SEQUENCE invite_code_id_seq CASCADE');
        $this->addSql('DROP TABLE invite_code');
    }
}
