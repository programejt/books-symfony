<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250310154655 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE books ALTER title TYPE VARCHAR(120)');
    $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(100)');
    $this->addSql('ALTER TABLE "user" ALTER role TYPE VARCHAR(60)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(180)');
    $this->addSql('ALTER TABLE "user" ALTER role TYPE VARCHAR(50)');
    $this->addSql('ALTER TABLE books ALTER title TYPE VARCHAR(255)');
  }
}
