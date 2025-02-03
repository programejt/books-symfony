<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241122132737 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE "user" ADD new_email VARCHAR(100) DEFAULT NULL');
    $this->addSql('ALTER TABLE "user" RENAME COLUMN is_verified TO email_verified');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE "user" DROP new_email');
    $this->addSql('ALTER TABLE "user" RENAME COLUMN email_verified TO is_verified');
  }
}
