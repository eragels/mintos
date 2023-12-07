<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231205184004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds unique constraint for client_id and currency_id in the account table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account ADD CONSTRAINT client_currency_unique UNIQUE (client_id, currency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP CONSTRAINT client_currency_unique');
    }
}
