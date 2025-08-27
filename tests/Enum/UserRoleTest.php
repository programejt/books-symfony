<?php

namespace Test\Enum;

use App\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class UserRoleTest extends WebTestCase
{
  #[DataProvider('userRoleSuccessDataProvider')]
  public function testUserRoleSuccess(
    string $roleKey,
    string $roleName,
    UserRole $role,
  ): void {
    $userRole = UserRole::from($roleKey);

    $this->assertSame($userRole, $role);
    $this->assertSame($userRole->toRoleName(), $roleName);
  }

  public static function userRoleSuccessDataProvider(): \Generator
  {
    yield 'admin' => [
      'roleKey' => 'ROLE_ADMIN',
      'roleName' => 'Admin',
      'role' => UserRole::Admin,
    ];

    yield 'moderator' => [
      'roleKey' => 'ROLE_MODERATOR',
      'roleName' => 'Moderator',
      'role' => UserRole::Moderator,
    ];

    yield 'user' => [
      'roleKey' => 'ROLE_USER',
      'roleName' => 'User',
      'role' => UserRole::User,
    ];
  }

  public function testUserRoleFailure(): void {
    $this->expectException(\ValueError::class);
    $this->expectExceptionMessage('"ROLE_NOT_EXISTS" is not a valid backing value for enum App\Enum\UserRole');

    UserRole::from('ROLE_NOT_EXISTS');
  }
}
