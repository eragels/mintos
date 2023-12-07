<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231207111222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds initial currencies to the currency table if it is empty';
    }

    public function up(Schema $schema): void
    {
        $result = $this->connection->executeQuery('SELECT COUNT(*) FROM currency')->fetchOne();

        if ($result == 0) {
            $this->addSql("INSERT INTO currency (id, name) VALUES (1, 'eur')");
            $this->addSql("INSERT INTO currency (id, name) VALUES (2, 'usd')");
            $this->addSql("INSERT INTO currency (id, name) VALUES (3, 'gbp')");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM currency WHERE id IN (1, 2, 3)");
    }
}
