<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use App\Event\BookAddEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class BookAddEventListener {
  public function __construct(
    private LoggerInterface $booksLogger
  ) {}

  public function __invoke(BookAddEvent $event): void
  {
    $book = $event->getBook();

    $this->booksLogger->info('Added book "'.$book->getTitle(). '" by "'.$book->getAuthorsNames().'"');
  }
}