<?php

namespace App\Validator;

use App\Entity\User;

#[\Attribute]
class UniqueEmail extends UniqueEntity
{
  public function __construct(
    string $field = 'email',
    ?array $groups = null,
    $payload = null,
  ) {
    parent::__construct(
      User::class,
      $field,
      'unique.email',
      $groups,
      $payload,
    );
  }

  public function validatedBy(): string
  {
    return UniqueEntityValidator::class;
  }
}
