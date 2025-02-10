<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class UserNameRequirements extends Assert\Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new Assert\NotBlank,
      new Assert\Length(
        min: 2,
        max: 255
      ),
    ];
  }
}
