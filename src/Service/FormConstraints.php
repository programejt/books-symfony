<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\Image;

class FormConstraints
{
  public static function getForPhoto(): Image
  {
    return new Image(
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
    );
  }
}