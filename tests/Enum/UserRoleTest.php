<?php

namespace App\Tests\Enum;

use App\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserRoleTest extends WebTestCase
{
  public function testUserRole(): void
  {
    $this->assertSame(
      UserRole::from('ROLE_ADMIN'),
      UserRole::Admin
    );

    $this->assertSame(
      UserRole::from('ROLE_MODERATOR'),
      UserRole::Moderator
    );

    $this->assertSame(
      UserRole::from('ROLE_USER'),
      UserRole::User
    );

    $this->expectException(\ValueError::class);
    $this->expectExceptionMessage('"ROLE_NOT_EXISTS" is not a valid backing value for enum App\Enum\UserRole');

    UserRole::from('ROLE_NOT_EXISTS');
  }
}
