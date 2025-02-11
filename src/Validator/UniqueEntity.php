<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEntity extends Constraint
{
  public function __construct(
    public readonly string $entityClass,
    public readonly string $field,
    public string $message,
    ?array $groups = null,
    $payload = null,
  ) {
    parent::__construct([], $groups, $payload);
  }
}
