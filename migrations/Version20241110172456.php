<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241110172456 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
    $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(60) NOT NULL, is_verified BOOLEAN NOT NULL, photo VARCHAR(60) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, password_changed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_NAME ON "user" (name)');
    $this->addSql('ALTER TABLE books ALTER id DROP DEFAULT');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
    $this->addSql('DROP TABLE "user"');
    $this->addSql('CREATE SEQUENCE books_id_seq');
    $this->addSql('SELECT setval(\'books_id_seq\', (SELECT MAX(id) FROM books))');
    $this->addSql('ALTER TABLE books ALTER id SET DEFAULT nextval(\'books_id_seq\')');
  }
}
