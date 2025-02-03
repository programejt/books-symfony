<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250124163303 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('CREATE UNIQUE INDEX UNIQ_4A1B2A92CC1CF4E6 ON books (isbn)');
    $this->addSql('ALTER TABLE books ALTER description TYPE VARCHAR(3000)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP INDEX UNIQ_4A1B2A92CC1CF4E6');
    $this->addSql('ALTER TABLE books ALTER description TYPE VARCHAR(255)');
  }
}
