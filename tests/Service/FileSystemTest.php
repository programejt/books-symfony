<?php

namespace Test\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\FileSystem;

final class FileSystemTest extends WebTestCase
{
  private ?string $testDir = null;

  public function setUp(): void
  {
    if (!$this->testDir) {
      self::bootKernel();

      $this->testDir = self::$kernel->getProjectDir().'/var/test';

      if (!\file_exists($this->testDir)) {
        \mkdir($this->testDir);
      }
    }
  }

  public function testDeleteFile(): void
  {
    $file = $this->testDir.'/test.txt';

    \touch($file);

    $this->assertTrue(FileSystem::deleteFile($file));
    $this->assertFalse(FileSystem::deleteFile($file));
  }

  public function testDeleteDir(): void
  {
    $dir1 = $this->testDir.'/dir1';
    $dir2 = "$dir1/dir2";

    $file1 = "$dir1/file1.txt";
    $file2 = "$dir1/file2.txt";
    $file3 = "$dir2/file3.txt";

    if (!\file_exists($dir1)) {
      \mkdir($dir1);
    }

    if (!\file_exists($dir2)) {
      \mkdir($dir2);
    }

    if (!\file_exists($file1)) {
      \touch($file1);
    }

    if (!\file_exists($file2)) {
      \touch($file2);
    }

    if (!\file_exists($file3)) {
      \touch($file3);
    }

    $this->assertTrue(FileSystem::deleteDir($dir1));
    $this->assertFalse(FileSystem::deleteDir($dir1));
  }

  public function testGetDocumentRoot(): void
  {
    $this->assertSame(
      '',
      FileSystem::getDocumentRoot(),
    );

    $_SERVER['DOCUMENT_ROOT'] = 'root';

    $this->assertSame(
      'root/',
      FileSystem::getDocumentRoot(),
    );
  }

  public function testEscapePath(): void
  {
    $this->assertSame(
      '//yyy',
      FileSystem::escapePath('../../yy\y'),
    );
  }
}