<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PasswordValid extends Constraint
{
  public string $message = 'password.current';
}
