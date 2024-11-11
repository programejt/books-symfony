<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240920202752 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    $this->abortIf(
      !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
      "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
    );

    $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_75ea56e016ba31db ON messenger_messages (delivered_at)');
    $this->addSql('CREATE INDEX idx_75ea56e0e3bd61ce ON messenger_messages (available_at)');
    $this->addSql('CREATE INDEX idx_75ea56e0fb7336f0 ON messenger_messages (queue_name)');
    $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
    $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
    $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
    $this->abortIf(
      !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
      "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
    );

    $this->addSql('CREATE TABLE books (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, year SMALLINT NOT NULL, isbn BIGINT NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
  }

  public function down(Schema $schema): void
  {
    $this->abortIf(
      !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
      "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
    );

    $this->addSql('DROP TABLE messenger_messages');
    $this->abortIf(
      !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform,
      "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'."
    );

    $this->addSql('DROP TABLE books');
  }
}
