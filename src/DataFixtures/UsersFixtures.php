<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
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

    $user
      ->setName("Admin")
      ->setEmail("admin@symfonybooks.com")
      ->setEmailVerified(true)
      ->setPassword(
        $this->passwordHasher->hashPassword($user, 'admin')
      )
      ->setRole(UserRole::Admin);

    $manager->persist($user);
    $manager->flush();
  }
}
