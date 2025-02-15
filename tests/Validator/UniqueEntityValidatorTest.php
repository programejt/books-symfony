<?php

namespace Test\Validator;

use App\Validator\UniqueEntityValidator;
use App\Repository\UserRepository;
use App\Validator\UniqueEntity;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use App\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Test\Helper\FakeConstraint;

final class UniqueEntityValidatorTest extends ConstraintValidatorTestCase
{
  private MockObject&EntityManagerInterface $entityManager;

  protected function createValidator(): UniqueEntityValidator
  {
    $this->entityManager = $this->createMock(EntityManagerInterface::class);

    return new UniqueEntityValidator(
      $this->entityManager,
    );
  }

  public function testIncorrectConstraint(): void
  {
    $this->expectException(UnexpectedTypeException::class);

    $this->validator->validate(null, new FakeConstraint);
  }

  public function testNullIsValid(): void
  {
    $this->validator->validate(null, $this->getConstraint());

    $this->assertNoViolation();
  }

  public function testEntityFound(): void
  {
    $constraint = $this->getConstraint();
    $userRepository = $this->createMock(UserRepository::class);
    $value = 'value';

    $this->entityManager
      ->expects(self::once())
      ->method('getRepository')
      ->with($constraint->entityClass)
      ->willReturn($userRepository);

    $userRepository
      ->expects(self::once())
      ->method('findOneBy')
      ->with([$constraint->field => $value])
      ->willReturn(new User);

    $this->validator->validate($value, $constraint);

    $this
      ->buildViolation($constraint->message)
      ->assertRaised();
  }

  public function testEntityNotFound(): void
  {
    $constraint = $this->getConstraint();
    $userRepository = $this->createMock(UserRepository::class);
    $value = 'value';

    $this->entityManager
      ->expects(self::once())
      ->method('getRepository')
      ->with($constraint->entityClass)
      ->willReturn($userRepository);

    $userRepository
      ->expects(self::once())
      ->method('findOneBy')
      ->with([$constraint->field => $value])
      ->willReturn(null);

    $this->validator->validate($value, $constraint);

    $this->assertNoViolation();
  }

  private function getConstraint(): UniqueEntity
  {
    return new UniqueEntity(
      User::class,
      'email',
      'message',
    );
  }
}
