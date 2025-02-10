<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class ImageRequirements extends Assert\Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new Assert\Image(
        maxSize: '5m',
        mimeTypes: [
          'image/jpg',
          'image/jpeg',
          'image/png',
          'image/webp',
          'image/avif',
          'image/heif',
        ],
        mimeTypesMessage: 'Please upload a valid image',
      ),
    ];
  }
}
