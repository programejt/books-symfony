<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Service\IsbnGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BooksFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
      for ($i = 0; $i < 1000005; ++$i) {
        $book = new Book();
        $book->setTitle("Book title $i");
        $book->setAuthor("Book author $i");
        $book->setDescription("Example description $i");
        $book->setYear(rand(1990, 2024));
        $book->setIsbn(IsbnGenerator::generate());

        $manager->persist($book);

        $manager->flush();
      }
    }
}
