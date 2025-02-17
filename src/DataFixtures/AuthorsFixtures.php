<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorsFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    foreach ($this->generateData() as $key => $data) {
      $author = new Author();
      $author->setName($data[0]);
      $author->setSurname($data[1]);

      $manager->persist($author);
      $manager->flush();

      $this->addReference('AUTHOR'.$key, $author);
    }
  }

  private function generateData(): \Generator
  {
    yield ['Jack', 'Smith'];
    yield ['Johnny', 'Tyler'];
    yield ['Amanda', 'Moon'];
    yield ['Susan', 'Sun'];
    yield ['Elon', 'Dawn'];
    yield ['Elizabeth', 'Stone'];
    yield ['Chris', 'Water'];
  }
}
