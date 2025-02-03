<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
  public function __construct(
    private UserPasswordHasherInterface $passwordHasher
  ) {}

  public function load(ObjectManager $manager): void
  {
    $user = new User();
    $user->setName("Admin");
    $user->setEmail("admin@symfonybooks.com");
    $user->setEmailVerified(true);
    $user->setPassword(
      $this->passwordHasher->hashPassword($user, 'admin')
    );

    $manager->persist($user);
    $manager->flush();
  }
}
