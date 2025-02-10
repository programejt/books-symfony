<?php

namespace Test\Entity;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserTest extends WebTestCase
{
  public function testId(): void
  {
    $user = new User;

    $this->assertNull($user->getId());
  }

  public function testName(): void
  {
    $user = new User;

    $this->assertNull($user->getName());

    $user->setName('Name');

    $this->assertSame('Name', $user->getName());
  }

  public function testRole(): void
  {
    $user = new User;

    $this->assertSame(
      UserRole::User,
      $user->getRole()
    );

    $this->assertSame(
      [UserRole::User->value],
      $user->getRoles()
    );

    $user->setRole(UserRole::Moderator);

    $this->assertSame(
      UserRole::Moderator,
      $user->getRole()
    );

    $this->assertSame(
      [UserRole::Moderator->value],
      $user->getRoles()
    );

    $user->setRole(UserRole::Admin);

    $this->assertSame(
      UserRole::Admin,
      $user->getRole()
    );

    $this->assertSame(
      [UserRole::Admin->value],
      $user->getRoles()
    );

    $user->setRole(UserRole::User);

    $this->assertSame(
      UserRole::User,
      $user->getRole()
    );

    $this->assertSame(
      [UserRole::User->value],
      $user->getRoles()
    );
  }

  public function testEmail(): void
  {
    $user = new User;

    $this->assertNull($user->getEmail());
    $this->assertSame('', $user->getUserIdentifier());

    $user->setEmail('email@email.com');

    $this->assertSame('email@email.com', $user->getEmail());
    $this->assertSame('email@email.com', $user->getUserIdentifier());
  }

  public function testNewEmail(): void
  {
    $user = new User;

    $this->assertNull($user->getNewEmail());

    $user->setNewEmail('email@email.com');

    $this->assertSame('email@email.com', $user->getNewEmail());
  }

  public function testPassword(): void
  {
    $user = new User;

    $this->assertNull($user->getPassword());

    $user->setPassword('password');

    $this->assertSame('password', $user->getPassword());
  }

  public function testPhoto(): void
  {
    $user = new User;

    $this->assertNull($user->getPhoto());

    $user->setPhoto('photo.png');

    $this->assertSame('photo.png', $user->getPhoto());
  }

  public function testEmailVerified(): void
  {
    $user = new User;

    $this->assertFalse($user->emailVerified());

    $user->setEmailVerified(true);

    $this->assertTrue($user->emailVerified());

    $user->setEmailVerified(false);

    $this->assertFalse($user->emailVerified());
  }

  public function testCreatedAt(): void
  {
    $user = new User;

    $this->assertInstanceOf(\DateTime::class, $user->getCreatedAt());
  }
}
