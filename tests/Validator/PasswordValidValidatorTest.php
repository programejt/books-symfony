<?php

namespace Test\Validator;

use App\Validator\PasswordValidValidator;
use App\Validator\PasswordValid;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use App\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordValidValidatorTest extends ConstraintValidatorTestCase
{
  use \Test\Helper\IncorrectConstraintTrait;

  private MockObject&Security $security;

  private MockObject&UserPasswordHasherInterface $userPasswordHasher;

  protected function createValidator(): PasswordValidValidator
  {
    $this->security = $this->createMock(Security::class);
    $this->userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

    return new PasswordValidValidator(
      $this->security,
      $this->userPasswordHasher,
    );
  }

  public function testNullIsNotValid(): void
  {
    $constraint = $this->getConstraint();
    $this->validator->validate(null, $constraint);

    $this
      ->buildViolation($constraint->message)
      ->assertRaised();
  }

  public function testPasswordIsValid(): void
  {
    $user = new User;
    $password = 'password';

    $this->security
      ->expects(self::once())
      ->method('getUser')
      ->willReturn($user);

    $this->userPasswordHasher
      ->expects(self::once())
      ->method('isPasswordValid')
      ->with($user, $password)
      ->willReturn(true);

    $this->validator->validate($password, $this->getConstraint());

    $this->assertNoViolation();
  }

  public function testPasswordIsNotValid(): void
  {
    $constraint = $this->getConstraint();
    $user = new User;
    $password = 'password';

    $this->security
      ->expects(self::once())
      ->method('getUser')
      ->willReturn($user);

    $this->userPasswordHasher
      ->expects(self::once())
      ->method('isPasswordValid')
      ->with($user, $password)
      ->willReturn(false);

    $this->validator->validate($password, $constraint);

    $this
      ->buildViolation($constraint->message)
      ->assertRaised();
  }

  private function getConstraint(): PasswordValid
  {
    return new PasswordValid;
  }
}
