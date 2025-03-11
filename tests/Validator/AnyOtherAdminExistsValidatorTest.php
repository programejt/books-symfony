<?php

namespace Test\Validator;

use App\Repository\UserRepository;
use App\Validator\AnyOtherAdminExistsValidator;
use App\Validator\AnyOtherAdminExists;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use PHPUnit\Framework\MockObject\MockObject;

final class AnyOtherAdminExistsValidatorTest extends ConstraintValidatorTestCase
{
  use \Test\Helper\IncorrectConstraintTrait;

  private MockObject&UserRepository $userRepository;

  protected function createValidator(): AnyOtherAdminExistsValidator
  {
    $this->userRepository = $this->createMock(UserRepository::class);

    return new AnyOtherAdminExistsValidator(
      $this->userRepository,
    );
  }

  public function testNullIsValid(): void
  {
    $this->validator->validate(null, new AnyOtherAdminExists);

    $this->assertNoViolation();
  }

  public function testNoResult(): void
  {
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $query = $this->createMock(Query::class);
    $user = new User;

    $this->userRepository
      ->expects(self::once())
      ->method('createQueryBuilder')
      ->with('u')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('select')
      ->with('u.id')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('where')
      ->with('u.role = :role')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('andWhere')
      ->with('u.id != :userId')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::exactly(2))
      ->method('setParameter')
      ->withAnyParameters()
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('getQuery')
      ->willReturn($query);

    $query
      ->expects(self::once())
      ->method('setMaxResults')
      ->with(1)
      ->willReturn($query);

    $query
      ->expects(self::once())
      ->method('getResult')
      ->willReturn(null);

    $constraint = new AnyOtherAdminExists;

    $this->validator->validate($user, $constraint);

    $this
      ->buildViolation($constraint->message)
      ->assertRaised();
  }

  public function testOtherAdminFound(): void
  {
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $query = $this->createMock(Query::class);
    $user = new User;

    $this->userRepository
      ->expects(self::once())
      ->method('createQueryBuilder')
      ->with('u')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('select')
      ->with('u.id')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('where')
      ->with('u.role = :role')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('andWhere')
      ->with('u.id != :userId')
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::exactly(2))
      ->method('setParameter')
      ->withAnyParameters()
      ->willReturn($queryBuilder);

    $queryBuilder
      ->expects(self::once())
      ->method('getQuery')
      ->willReturn($query);

    $query
      ->expects(self::once())
      ->method('setMaxResults')
      ->with(1)
      ->willReturn($query);

    $query
      ->expects(self::once())
      ->method('getResult')
      ->willReturn(new User);

    $this->validator->validate($user, new AnyOtherAdminExists);

    $this->assertNoViolation();
  }
}
