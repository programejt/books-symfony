<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\User;
use App\Service\IsbnGenerator;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookControllerTest extends WebTestCase
{
  private KernelBrowser $client;
  private EntityManagerInterface $manager;
  private BookRepository $repository;
  private User $user;
  private Author $author;
  private string $path = '/books/';

  protected function setUp(): void
  {
    $this->client = static::createClient();
    $this->manager = static::getContainer()->get('doctrine')->getManager();
    $this->repository = $this->manager->getRepository(Book::class);

    foreach ($this->repository->findAll() as $book) {
      $this->manager->remove($book);
    }

    $userRepository = $this->manager->getRepository(User::class);

    foreach ($userRepository->findAll() as $user) {
      $this->manager->remove($user);
    }

    $authorRepository = $this->manager->getRepository(Author::class);

    foreach ($authorRepository->findAll() as $author) {
      $this->manager->remove($author);
    }

    $this->manager->flush();

    $user = new User();
    $user->setName('testUser');
    $user->setEmail('testUser@symfonybooks.com');
    $user->setPassword('passwrd');

    $author = new Author;
    $author->setName('Jack');
    $author->setSurname('Moon');

    $this->manager->persist($user);
    $this->manager->persist($author);
    $this->manager->flush();

    $this->user = $user;
    $this->author = $author;
  }

  public function testIndex(): void
  {
    $this->client->followRedirects();
    $crawler = $this->client->request('GET', $this->path);

    self::assertResponseStatusCodeSame(200);
    self::assertPageTitleContains('Books');

    // Use the $crawler to perform additional assertions e.g.
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

    $book = $this->repository->findAll()[0];

    self::assertResponseRedirects($this->path.$book->getId());

    self::assertSame(1, $this->repository->count([]));
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

    $this->manager->persist($book);
    $this->manager->flush();

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

    $this->manager->persist($book);
    $this->manager->persist($author);

    $this->manager->flush();

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

    $book = $this->repository->findAll();
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

    $this->manager->persist($book);
    $this->manager->flush();

    $this->loginUser();

    $this->client->request('GET', sprintf('%s%s', $this->path, $book->getId()));

    self::assertResponseStatusCodeSame(200);
    $this->client->submitForm('Delete');

    self::assertResponseRedirects('/books');
    self::assertSame(0, $this->repository->count([]));
  }

  private function loginUser(): void
  {
    $this->client->loginUser($this->user);
  }
}
