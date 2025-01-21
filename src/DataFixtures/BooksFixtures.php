<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Service\IsbnGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BooksFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    for ($i = 0; $i < 105; ++$i) {
      $book = new Book();
      $book->setTitle("Book title $i");
      $book->addAuthor(
        $this->getReference('AUTHOR'.rand(1, 50), Author::class)
      );
      $book->setDescription("Example description $i");
      $book->setYear(rand(1950, 2024));
      $book->setIsbn(IsbnGenerator::generate());

      $manager->persist($book);

      $manager->flush();
    }
  }
}
