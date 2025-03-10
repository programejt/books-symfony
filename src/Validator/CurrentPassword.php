<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class CurrentPassword extends Assert\Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new PasswordValid,
    ];
  }
}
