<?php

namespace Test\Entity;

use App\Entity\Author;
use App\Entity\Book;
use App\Service\IsbnGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookTest extends WebTestCase
{
  public function testId(): void
  {
    $book = new Book;

    $this->assertNull($book->getId());

    $book->setId(1);

    $this->assertSame(1, $book->getId());
  }

  public function testTitle(): void
  {
    $book = new Book;

    $this->assertNull($book->getTitle());

    $book->setTitle('Title');

    $this->assertSame('Title', $book->getTitle());
  }

  public function testAuthors(): void
  {
    $book = new Book;
    $author = new Author;

    $this->assertCount(0, $book->getAuthors());

    $book->addAuthor($author);

    $this->assertCount(1, $book->getAuthors());

    $book->addAuthor($author);

    $this->assertCount(1, $book->getAuthors());

    $book->addAuthor(new Author);

    $this->assertCount(2, $book->getAuthors());

    $book->removeAuthor($author);

    $this->assertCount(1, $book->getAuthors());
  }

  public function testYear(): void
  {
    $book = new Book;

    $this->assertNull($book->getYear());

    $book->setYear(2025);

    $this->assertSame(2025, $book->getYear());
  }

  public function testIsbn(): void
  {
    $book = new Book;
    $isbn = IsbnGenerator::generate();

    $this->assertNull($book->getIsbn());

    $book->setIsbn($isbn);

    $this->assertSame($isbn, $book->getIsbn());
  }

  public function testDescription(): void
  {
    $book = new Book;

    $this->assertNull($book->getDescription());

    $book->setDescription('Description');

    $this->assertSame('Description', $book->getDescription());
  }
}
