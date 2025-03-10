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
        maxSize: '5Mi',
        mimeTypes: [
          'image/jpg',
          'image/jpeg',
          'image/png',
          'image/webp',
          'image/avif',
          'image/heif',
        ],
        mimeTypesMessage: 'image.mime_types',
        maxSizeMessage: 'file.max_size',
        filenameTooLongMessage: 'file.name_too_long',
        uploadIniSizeErrorMessage: 'file.max_size',
        uploadFormSizeErrorMessage: 'file.max_size',
        uploadPartialErrorMessage: 'file.partial',
        uploadNoFileErrorMessage: 'file.no_file',
        uploadNoTmpDirErrorMessage: 'file.no_tmp_dir',
        uploadCantWriteErrorMessage: 'file.cant_write',
        uploadExtensionErrorMessage: 'file.extension',
        uploadErrorMessage: 'file.upload',
        sizeNotDetectedMessage: 'file.size_not_detected',
      ),
    ];
  }
}
