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
        'message' => 'Please enter a password'
      ]),
      new Assert\Length([
        'min' => 6,
        'minMessage' => 'Your password should be at least {{ limit }} characters',
        'max' => 255,
        'maxMessage' => 'Your password should be max {{ limit }} characters'
      ]),
    ];
  }
}
