<?php

namespace Test\Helper;

use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Test\Helper\FakeConstraint;

trait IncorrectConstraintTrait
{
  public function testIncorrectConstraint(): void
  {
    $this->expectException(UnexpectedTypeException::class);

    $this->validator->validate(null, new FakeConstraint);
  }
}