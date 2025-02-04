<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250204093443 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE author_book DROP CONSTRAINT author_book_pkey');
    $this->addSql('ALTER TABLE author_book ADD PRIMARY KEY (book_id, author_id)');
    $this->addSql('ALTER TABLE "user" ADD role VARCHAR(50) NOT NULL');
    $this->addSql('ALTER TABLE "user" DROP roles');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE "user" ADD roles JSON NOT NULL');
    $this->addSql('ALTER TABLE "user" DROP role');
    $this->addSql('DROP INDEX author_book_pkey');
    $this->addSql('ALTER TABLE author_book ADD PRIMARY KEY (author_id, book_id)');
  }
}
