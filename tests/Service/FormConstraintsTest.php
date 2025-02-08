<?php

namespace App\Test\Service;

use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\FormConstraints;

final class FormConstraintsTest extends WebTestCase
{
  public function testGetForPhoto(): void
  {
    $constraint = FormConstraints::getForPhoto();

    $this->assertInstanceOf(
      Image::class,
      $constraint,
    );

    $this->assertSame(
      5000000,
      $constraint->maxSize,
    );

    $this->assertSame(
      [
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif',
        'image/heif',
      ],
      $constraint->mimeTypes,
    );

    $this->assertSame(
      'Please upload a valid image',
      $constraint->mimeTypesMessage,
    );
  }
}