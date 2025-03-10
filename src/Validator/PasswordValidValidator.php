<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class PasswordValidValidator extends ConstraintValidator
{
  public function __construct(
    private readonly Security $security,
    private readonly UserPasswordHasherInterface $userPasswordHasher,
  ) {}

  public function validate(
    mixed $value,
    Constraint $constraint,
  ): void {
    if (!$constraint instanceof PasswordValid) {
      throw new UnexpectedTypeException($constraint, PasswordValid::class);
    }

    if (null === $value) {
      $this->buildViolation($constraint);
      return;
    }

    /** @var User $user */
    $user = $this->security->getUser();

    if (!$this->userPasswordHasher->isPasswordValid($user, $value)) {
      $this->buildViolation($constraint);
    }
  }

  private function buildViolation(PasswordValid $constraint): void
  {
    $this->context
      ->buildViolation($constraint->message)
      ->addViolation();
  }
}
