<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class PasswordRequirements extends Assert\Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new Assert\NotBlank([
        'message' => 'password.not_blank',
      ]),
      new Assert\Length([
        'min' => 6,
        'minMessage' => 'password.length.min',
        'max' => 255,
        'maxMessage' => 'password.length.max',
      ]),
    ];
  }
}
