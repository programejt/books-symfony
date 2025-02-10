<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250210163018 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_NEW_EMAIL ON "user" (new_email)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP INDEX UNIQ_IDENTIFIER_NEW_EMAIL');
  }
}
