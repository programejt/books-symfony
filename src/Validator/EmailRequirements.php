<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

#[\Attribute]
class EmailRequirements extends Assert\Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new Assert\NotBlank(
        message: 'email.not_blank',
      ),
      new Assert\Email(
        message: 'email.not_valid',
      ),
      new Assert\Length(
        max: User::EMAIL_MAX_LENGTH,
        maxMessage: 'email.length.max',
      ),
    ];
  }
}