<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\IsbnGenerator;
use Symfony\Component\Validator\Constraints\Isbn;
use Symfony\Component\Validator\Constraints\IsbnValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use PHPUnit\Framework\Attributes\DataProvider;

final class IsbnGeneratorTest extends WebTestCase
{
  #[DataProvider('generateDataProvider')]
  public function testGenerate(
    int $isbnValue,
    int $methodsCount,
    ?string $message,
  ): void {
    $isbn = new Isbn(Isbn::ISBN_13);
    $validator = new IsbnValidator;
    $context = $this->createMock(ExecutionContextInterface::class);

    $validator->initialize($context);

    $context
      ->expects($this->exactly($methodsCount))
      ->method('buildViolation')
      ->with($message)
      ->willReturn($this->createMock(ConstraintViolationBuilderInterface::class));

    $validator->validate($isbnValue, $isbn);
  }

  public static function generateDataProvider(): \Generator
  {
    yield 'success' => [
      'isbnValue' => IsbnGenerator::generate(),
      'methodsCount' => 0,
      'message' => null,
    ];

    yield 'failure' => [
      'isbnValue' => 1423423,
      'methodsCount' => 1,
      'message' => (new Isbn)->isbn13Message,
    ];
  }
}
