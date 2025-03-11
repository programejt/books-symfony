<?php

namespace App\Service;

class FileSystem
{
  public const string IMAGES_DIR = 'assets/images';

  public static function deleteFile(string $filePath): bool
  {
    $filePath = self::escapePath($filePath);

    if (\file_exists($filePath) && \is_file($filePath)) {
      return \unlink($filePath);
    }

    return false;
  }

  public static function deleteDir(string $dir): bool
  {
    $dir = self::escapePath($dir);

    if (!\is_dir($dir)) {
      return false;
    }

    $scan = \scandir($dir);

    if (!$scan) {
      return false;
    }

    $failure = false;

    for ($i = 2, $imax = \count($scan); $i < $imax; ++$i) {
      $file = $dir."/".$scan[$i];

      if (\is_file($file)) {
        if (!\unlink($file)) {
          $failure = true;
        }
      } else if (\is_dir($file)) {
        if (!self::deleteDir($file)) {
          $failure = true;
        }

        if (!\rmdir($file)) {
          $failure = true;
        }
      }
    }

    if (!\rmdir($dir)) {
      return false;
    }

    return !$failure;
  }

  public static function getDocumentRoot(): string
  {
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];

    if ($documentRoot && $documentRoot[-1] !== '/') {
      return $documentRoot.'/';
    }

    return $documentRoot;
  }

  public static function escapePath(string $path): string
  {
    return \str_replace(['..', '\\'], '', $path);
  }
}