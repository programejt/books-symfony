<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use App\Event\BookAddEvent;
// use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

// #[AsEventListener]
class BookAddEventListener {
  public function __construct(
    private LoggerInterface $booksLogger
  ) {}

  public function onBookAdd(BookAddEvent $event) {
    $this->booksLogger->info('Added book "'.$event->getBook()->getTitle(). '" by "'.$event->getBook()->getAuthor().'"');
  }
}