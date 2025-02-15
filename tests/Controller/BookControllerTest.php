<?php

namespace Test\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\User;
use App\Enum\UserRole;
use App\Service\IsbnGenerator;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookControllerTest extends WebTestCase
{
  private KernelBrowser $client;
  private EntityManagerInterface $entityManager;
  private BookRepository $bookRepository;
  private User $user;
  private Author $author;
  private string $path = '/books/';

  protected function setUp(): void
  {
    $this->client = static::createClient();
    $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    $this->bookRepository = $this->entityManager->getRepository(Book::class);

    foreach ($this->bookRepository->findAll() as $book) {
      $this->entityManager->remove($book);
    }

    $userRepository = $this->entityManager->getRepository(User::class);

    foreach ($userRepository->findAll() as $user) {
      $this->entityManager->remove($user);
    }

    $authorRepository = $this->entityManager->getRepository(Author::class);

    foreach ($authorRepository->findAll() as $author) {
      $this->entityManager->remove($author);
    }

    $this->entityManager->flush();

    $user = new User();
    $user
      ->setName('testUser')
      ->setEmail('testUser@symfonybooks.com')
      ->setRole(UserRole::Moderator)
      ->setPassword('passwrd');

    $author = new Author;
    $author
      ->setName('Jack')
      ->setSurname('Moon');

    $this->entityManager->persist($user);
    $this->entityManager->persist($author);
    $this->entityManager->flush();

    $this->user = $user;
    $this->author = $author;
  }

  public function testIndex(): void
  {
    $this->client->followRedirects();
    $crawler = $this->client->request('GET', $this->path);

    self::assertResponseStatusCodeSame(200);
    self::assertPageTitleContains('Books');

    // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
  }

  public function testNew(): void
  {
    $uri = sprintf('%snew', $this->path);

    $this->client->request('GET', $uri);

    self::assertResponseStatusCodeSame(302);

    $this->loginUser();

    $this->client->request('GET', $uri);

    self::assertResponseStatusCodeSame(200);

    $this->client->submitForm('Add', [
      'book[title]' => 'Testing',
      'book[authors]' => [$this->author->getId()],
      'book[year]' => 2025,
      'book[isbn]' => IsbnGenerator::generate(),
      'book[description]' => 'Testing',
    ]);

    $book = $this->bookRepository->findAll()[0];

    self::assertResponseRedirects($this->path.$book->getId());

    self::assertSame(1, $this->bookRepository->count([]));
  }

  public function testShow(): void
  {
    $title = 'My Title';

    $book = new Book();
    $book->setId(10);
    $book->setTitle($title);
    $book->addAuthor($this->author);
    $book->setYear(2024);
    $book->setIsbn(IsbnGenerator::generate());
    $book->setDescription('My Title');

    $this->entityManager->persist($book);
    $this->entityManager->flush();

    $this->client->request('GET', sprintf('%s%s', $this->path, $book->getId()));

    self::assertResponseStatusCodeSame(200);
    self::assertPageTitleContains($title);
  }

  public function testEdit(): void
  {
    $book = new Book;
    $book->setTitle('Value');
    $book->addAuthor($this->author);
    $book->setYear(2024);
    $book->setIsbn(IsbnGenerator::generate());
    $book->setDescription('Value');

    $author = new Author;
    $author->setName('John');
    $author->setSurname('Sun');

    $this->entityManager->persist($book);
    $this->entityManager->persist($author);

    $this->entityManager->flush();

    $uri = sprintf('%s%s/edit', $this->path, $book->getId());

    $this->client->request('GET', $uri);

    self::assertResponseStatusCodeSame(302);

    $this->loginUser();

    $this->client->request('GET', $uri);

    self::assertResponseStatusCodeSame(200);

    $isbn = IsbnGenerator::generate();

    $this->client->submitForm('Update', [
      'book[title]' => 'Something New',
      'book[authors][1]' => $author->getId(),
      'book[year]' => 2025,
      'book[isbn]' => $isbn,
      'book[description]' => 'Something New',
      'book[photo]' => null,
    ]);

    self::assertResponseRedirects($this->path.$book->getId());

    $book = $this->bookRepository->findAll();
    $book = $book[0];

    $authors = $book->getAuthors();

    self::assertSame('Something New', $book->getTitle());
    self::assertSame($this->author->getId(), $authors[0]->getId());
    self::assertSame($author->getId(), $authors[1]->getId());
    self::assertSame(2025, $book->getYear());
    self::assertSame($isbn, $book->getIsbn());
    self::assertSame('Something New', $book->getDescription());
    self::assertNull($book->getPhoto());
  }

  public function testRemove(): void
  {
    $book = new Book();
    $book->setTitle('Value');
    $book->addAuthor($this->author);
    $book->setYear(2024);
    $book->setIsbn(IsbnGenerator::generate());
    $book->setDescription('Value');

    $this->entityManager->persist($book);
    $this->entityManager->flush();

    $this->loginUser();

    $this->client->request('GET', sprintf('%s%s', $this->path, $book->getId()));

    self::assertResponseStatusCodeSame(200);
    $this->client->submitForm('Delete');

    self::assertResponseRedirects('/books');
    self::assertSame(0, $this->bookRepository->count([]));
  }

  private function loginUser(): void
  {
    $this->client->loginUser($this->user);
  }
}
