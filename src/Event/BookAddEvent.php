<?php

namespace App\Event;

use App\Entity\Book;
use Symfony\Contracts\EventDispatcher\Event;

class BookAddEvent extends Event
{
  public const string NAME = 'book.add';

  public function __construct(
    private readonly Book $book,
  ) {}

  public function getBook(): Book
  {
    return $this->book;
  }
}