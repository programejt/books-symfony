<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManagerInterface;

class UniqueEntityValidator extends ConstraintValidator
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
  ) {}

  public function validate(
    mixed $value,
    Constraint $constraint,
  ): void {
    if (!$constraint instanceof UniqueEntity) {
      throw new UnexpectedTypeException($constraint, UniqueEntity::class);
    }

    if (null === $value) {
      return;
    }

    $entity = $this->entityManager
      ->getRepository($constraint->entityClass)
      ->findOneBy([$constraint->field => $value]);

    if ($entity) {
      $this->context
        ->buildViolation($constraint->message)
        ->addViolation();
    }
  }
}
