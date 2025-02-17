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
    foreach ($this->generateData() as $data) {
      $book = new Book();
      $book->setTitle($data[0]);

      for ($i = 0; $i < rand(1, 4); ++$i) {
        $book->addAuthor(
          $this->getReference(
            'AUTHOR'.$i,
            Author::class,
          )
        );
      }

      $book->setYear(rand(1950, 2025));
      $book->setIsbn(IsbnGenerator::generate());
      $book->setDescription($data[1]);

      $manager->persist($book);
      $manager->flush();
    }
  }

  private function generateData(): \Generator
  {
    yield ['Amazing stories', 'Book about many amazing and fascinating stories'];
    yield ['Space', 'Book about our universe - planets, stars, etc.'];
    yield ['Web development', 'Book about web development, modern technologies, good practises etc.'];
    yield ['Operating systems', 'List of modern operating systems with description, pros and cons of each of them, etc.'];
    yield ['Dangerous Animals', 'The most dangerous animals in the world, regions of occurrence, ways of life etc.'];
    yield ['Life hacks', 'List with description of many very useful life hacks that help you in everyday situations'];
    yield ['Web browsers', 'List of web browsers with all features and descriptions of them, pros and cons etc.'];
    yield ['Audio universe', 'Book for people that want to learn many details about audio hardware, software - how it works, how it is built, etc.'];
    yield ['Beautiful places', 'List of many beautiful places in the world with photos and detailed description'];
    yield ['Your best friend - dog', 'Book about our best friends - dogs. List of all dog types with description, how to take care of them etc.'];
    yield ['Full health', 'Book that describe how to take care about your physical and mental health'];
    yield ['Under water', 'What secrets are hidden in the underwater world'];
  }
}
