<?php

namespace App\Tests\Entity;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthorTest extends WebTestCase
{
  public function testId(): void
  {
    $author = new Author;

    $this->assertNull($author->getId());

    $author->setId(1);

    $this->assertSame(1, $author->getId());
  }

  public function testName(): void
  {
    $author = new Author;

    $this->assertNull($author->getName());

    $author->setName('Name');

    $this->assertSame('Name', $author->getName());
  }

  public function testSurname(): void
  {
    $author = new Author;

    $this->assertNull($author->getSurname());

    $author->setSurname('Surname');

    $this->assertSame('Surname', $author->getSurname());
  }

  public function testBooks(): void
  {
    $author = new Author;
    $book = new Book;

    $this->assertCount(0, $author->getBooks());

    $author->addBook($book);

    $this->assertCount(1, $author->getBooks());

    $author->addBook($book);

    $this->assertCount(1, $author->getBooks());

    $author->addBook(new Book);

    $this->assertCount(2, $author->getBooks());

    $author->removeBook($book);

    $this->assertCount(1, $author->getBooks());
  }

  public function testToString(): void
  {
    $author = new Author;

    $this->assertSame(' ', (string) $author);

    $author
      ->setName('Name')
      ->setSurname('Surname');

    $this->assertSame('Name Surname', (string) $author);
  }
}
