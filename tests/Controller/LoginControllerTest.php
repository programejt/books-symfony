<?php

namespace Test\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoginControllerTest extends WebTestCase
{
  private KernelBrowser $client;

  protected function setUp(): void
  {
    $this->client = static::createClient();
    $container = static::getContainer();
    $em = $container->get('doctrine.orm.entity_manager');
    $userRepository = $em->getRepository(User::class);

    // Remove any existing users from the test database
    foreach ($userRepository->findAll() as $user) {
      $em->remove($user);
    }

    $em->flush();

    // Create a User fixture
    /** @var UserPasswordHasherInterface $passwordHasher */
    $passwordHasher = $container->get('security.user_password_hasher');

    $user = (new User())
      ->setEmail('email@example.com')
      ->setName('Test User');

    $user->setPassword($passwordHasher->hashPassword($user, 'password'));

    $em->persist($user);
    $em->flush();
  }

  public function testLogin(): void
  {
    // Denied - Can't login with invalid email address.
    $this->client->request('GET', '/login');
    self::assertResponseIsSuccessful();

    $this->client->submitForm('Log in', [
      '_username' => 'doesNotExist@example.com',
      '_password' => 'password',
    ]);

    self::assertResponseRedirects('/login');
    $this->client->followRedirect();

    // Ensure we do not reveal if the user exists or not.
    self::assertSelectorTextContains('.alert-danger', 'Invalid credentials.');

    // Denied - Can't login with invalid password.
    $this->client->request('GET', '/login');
    self::assertResponseIsSuccessful();

    $this->client->submitForm('Log in', [
      '_username' => 'email@example.com',
      '_password' => 'bad-password',
    ]);

    self::assertResponseRedirects('/login');
    $this->client->followRedirect();

    // Ensure we do not reveal the user exists but the password is wrong.
    self::assertSelectorTextContains('.alert-danger', 'Invalid credentials.');

    // Success - Login with valid credentials is allowed.
    $this->client->submitForm('Log in', [
      '_username' => 'email@example.com',
      '_password' => 'password',
    ]);

    self::assertResponseRedirects('/books');
    $this->client->followRedirect();

    self::assertSelectorNotExists('.alert-danger');
    self::assertResponseIsSuccessful();
  }
}
