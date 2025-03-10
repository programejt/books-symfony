<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

#[\Attribute]
class UserNameRequirements extends Assert\Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new Assert\NotBlank(message: 'not_blank'),
      new Assert\Length(
        min: 2,
        max: User::NAME_MAX_LENGTH,
        minMessage: 'length.min',
        maxMessage: 'length.max',
      ),
    ];
  }
}
