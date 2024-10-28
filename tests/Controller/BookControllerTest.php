<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Service\IsbnGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookControllerTest extends WebTestCase
{
  private KernelBrowser $client;
  private EntityManagerInterface $manager;
  private EntityRepository $repository;
  private string $path = '/books/';

  protected function setUp(): void
  {
    $this->client = static::createClient();
    $this->manager = static::getContainer()->get('doctrine')->getManager();
    $this->repository = $this->manager->getRepository(Book::class);

    foreach ($this->repository->findAll() as $object) {
      $this->manager->remove($object);
    }

    $this->manager->flush();
  }

  public function testIndex(): void
  {
    $this->client->followRedirects();
    $crawler = $this->client->request('GET', $this->path);

    self::assertResponseStatusCodeSame(200);
    self::assertPageTitleContains('Book index');

    // Use the $crawler to perform additional assertions e.g.
    // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
  }

  public function testNew(): void
  {
    $this->markTestIncomplete();
    $this->client->request('GET', sprintf('%snew', $this->path));

    self::assertResponseStatusCodeSame(200);

    $this->client->submitForm('Save', [
      'book[title]' => 'Testing',
      'book[author]' => 'Testing',
      'book[description]' => 'Testing',
      'book[year]' => 'Testing',
      'book[isbn]' => 'Testing',
      'book[photo]' => 'Testing',
    ]);

    self::assertResponseRedirects($this->path);

    self::assertSame(1, $this->repository->count([]));
  }

  public function testShow(): void
  {
    $this->markTestIncomplete();
    $fixture = new Book();
    $fixture->setTitle('My Title');
    $fixture->setAuthor('My Title');
    $fixture->setDescription('My Title');
    $fixture->setYear(rand(1990, 2024));
    $fixture->setIsbn(IsbnGenerator::generate());
    $fixture->setPhoto('My Title');

    $this->manager->persist($fixture);
    $this->manager->flush();

    $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

    self::assertResponseStatusCodeSame(200);
    self::assertPageTitleContains('Book');

    // Use assertions to check that the properties are properly displayed.
  }

  public function testEdit(): void
  {
    $this->markTestIncomplete();
    $fixture = new Book();
    $fixture->setTitle('Value');
    $fixture->setAuthor('Value');
    $fixture->setDescription('Value');
    $fixture->setYear(rand(1990, 2024));
    $fixture->setIsbn(IsbnGenerator::generate());
    $fixture->setPhoto('Value');

    $this->manager->persist($fixture);
    $this->manager->flush();

    $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

    $this->client->submitForm('Update', [
      'book[title]' => 'Something New',
      'book[author]' => 'Something New',
      'book[description]' => 'Something New',
      'book[year]' => 'Something New',
      'book[isbn]' => 'Something New',
      'book[photo]' => 'Something New',
    ]);

    self::assertResponseRedirects('/books/');

    $fixture = $this->repository->findAll();

    self::assertSame('Something New', $fixture[0]->getTitle());
    self::assertSame('Something New', $fixture[0]->getAuthor());
    self::assertSame('Something New', $fixture[0]->getDescription());
    self::assertSame('Something New', $fixture[0]->getYear());
    self::assertSame('Something New', $fixture[0]->getIsbn());
    self::assertSame('Something New', $fixture[0]->getPhoto());
  }

  public function testRemove(): void
  {
    $this->markTestIncomplete();
    $fixture = new Book();
    $fixture->setTitle('Value');
    $fixture->setAuthor('Value');
    $fixture->setDescription('Value');
    $fixture->setYear(rand(1990, 2024));
    $fixture->setIsbn(IsbnGenerator::generate());
    $fixture->setPhoto('Value');

    $this->manager->persist($fixture);
    $this->manager->flush();

    $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
    $this->client->submitForm('Delete');

    self::assertResponseRedirects('/books/');
    self::assertSame(0, $this->repository->count([]));
  }
}
