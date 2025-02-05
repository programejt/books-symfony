<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\IsbnGenerator;
use Symfony\Component\Validator\Constraints\Isbn;
use Symfony\Component\Validator\Constraints\IsbnValidator;

final class IsbnGeneratorTest extends WebTestCase
{
  protected function setUp(): void {}

  public function testIsbnGeneration(): void
  {
    $isbn = new Isbn(Isbn::ISBN_13);

    (new IsbnValidator)->validate(
      IsbnGenerator::generate(),
      $isbn
    );

    self::assertNull($isbn->message);
  }
}
