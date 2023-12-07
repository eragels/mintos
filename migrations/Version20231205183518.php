<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205183518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT NOT NULL, client_id INT NOT NULL, currency_id INT NOT NULL, balance DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D3656A419EB6921 ON account (client_id)');
        $this->addSql('CREATE INDEX IDX_7D3656A438248176 ON account (currency_id)');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE currency (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE transaction (id INT NOT NULL, sender_account_id INT NOT NULL, receiver_account_id INT NOT NULL, currency_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D1CFEF0177 ON transaction (sender_account_id)');
        $this->addSql('CREATE INDEX IDX_723705D1D8CF5973 ON transaction (receiver_account_id)');
        $this->addSql('CREATE INDEX IDX_723705D138248176 ON transaction (currency_id)');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A438248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CFEF0177 FOREIGN KEY (sender_account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D8CF5973 FOREIGN KEY (receiver_account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D138248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A419EB6921');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A438248176');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1CFEF0177');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1D8CF5973');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D138248176');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE transaction');
    }
}
