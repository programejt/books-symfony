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

      for ($j = 0; $j < rand(1, 4); ++$j) {
        $book->addAuthor(
          $this->getReference(
            'AUTHOR'.$j,
            Author::class,
          )
        );
      }

      $book->setYear(rand(1950, 2025));
      $book->setIsbn(IsbnGenerator::generate());
      $book->setDescription("Example description $i");

      $manager->persist($book);
      $manager->flush();
    }
  }
}
