<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Enum\UserRole;

class AnyOtherAdminExistsValidator extends ConstraintValidator
{
  public function __construct(
    private UserRepository $userRepository,
  ) {
  }

  public function validate(mixed $value, Constraint $constraint): void
  {
    if (!$constraint instanceof AnyOtherAdminExists) {
      throw new UnexpectedTypeException($constraint, AnyOtherAdminExists::class);
    }

    if (null === $value) {
      return;
    }

    $result = $this->userRepository
      ->createQueryBuilder('u')
      ->select('u.id')
      ->where('u.role = :role')
      ->andWhere('u.id != :userId')
      ->setParameter('role', UserRole::Admin->value)
      ->setParameter('userId', $value->getId())
      ->getQuery()
      ->setMaxResults(1)
      ->getResult();

    if (!$result) {
      $this->context
        ->buildViolation($constraint->message)
        ->addViolation();
    }
  }
}
