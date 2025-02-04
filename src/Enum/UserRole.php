<?php

namespace App\Enum;

enum UserRole: string
{
  case Admin = 'ROLE_ADMIN';
  case User  = 'ROLE_USER';

  const ADMIN_VALUE = 0;
  const USER_VALUE  = 0;

  public static function fromInt(int $role): self
  {
    return match($role) {
      self::ADMIN_VALUE => self::Admin,
      self::USER_VALUE  => self::User,
    };
  }

  public function toRoleName(): string
  {
    return match($this) {
      self::Admin => 'Admin',
      self::User  => 'User',
    };
  }
}