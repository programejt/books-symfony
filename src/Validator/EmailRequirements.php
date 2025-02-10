<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class EmailRequirements extends Assert\Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new Assert\NotBlank(
        message: 'The email can\'t be empty',
      ),
      new Assert\Email(
        message: 'The email is not valid',
      ),
      new Assert\Length(
        min: 3,
        max: 180,
      ),
    ];
  }
}
