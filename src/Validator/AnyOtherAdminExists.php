<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AnyOtherAdminExists extends Constraint
{
  public string $message = 'any_other_admin';
}
