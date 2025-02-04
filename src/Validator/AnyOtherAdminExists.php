<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AnyOtherAdminExists extends Constraint
{
  public string $message = 'There is no any other admin in system.';
}
