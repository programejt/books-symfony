<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorsFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    for ($i = 0; $i < 105; ++$i) {
      $author = new Author();
      $author->setName("Author");
      $author->setSurname("Name $i");

      $manager->persist($author);

      $manager->flush();

      $this->addReference('AUTHOR'.$i, $author);
    }
  }
}
