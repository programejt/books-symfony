<?php

namespace App\Service;

class FileSystem
{
  public const string IMAGES_DIR = 'assets/images';

  public static function deleteFile(string $filePath): bool
  {
    $filePath = self::escapePath($filePath);

    if (file_exists($filePath) && is_file($filePath)) {
      return unlink($filePath);
    }

    return false;
  }

  public static function getDocumentRoot(): string
  {
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    if (substr($documentRoot, -1) !== '/') {
      $documentRoot .= '/';
    }
    return $documentRoot;
  }

  private static function escapePath(string $path): string
  {
    return str_replace(['..', '\\'], '', $path);
  }
}