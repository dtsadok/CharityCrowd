<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210209194955 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX idx_872662181d650ba4 RENAME TO IDX_872662187597D3FE');
        $this->addSql('CREATE UNIQUE INDEX vote_unique ON vote (member_id, nomination_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX idx_872662187597d3fe RENAME TO idx_872662181d650ba4');
        $this->addSql('DROP INDEX vote_unique');
    }
}
