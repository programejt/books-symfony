<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: "SEQUENCE")]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 100)]
  private ?string $name = null;

  #[ORM\Column(length: 100)]
  private ?string $surname = null;

  /**
   * @var Collection<int, Book>
   */
  #[ORM\ManyToMany(
    targetEntity: Book::class,
    mappedBy: 'authors',
    cascade: ['persist'],
  )]
  private Collection $books;

  public function __construct()
  {
    $this->books = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): static
  {
    $this->id = $id;

    return $this;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  public function getSurname(): ?string
  {
    return $this->surname;
  }

  public function setSurname(string $surname): static
  {
    $this->surname = $surname;

    return $this;
  }

  /**
   * @return Collection<int, Book>
   */
  public function getBooks(): Collection
  {
    return $this->books;
  }

  public function addBook(Book $book): static
  {
    if (!$this->books->contains($book)) {
      $this->books->add($book);
      // $book->addAuthor($this);
    }

    return $this;
  }

  public function removeBook(Book $book): static
  {
    if ($this->books->removeElement($book)) {
      // $book->removeAuthor($this);
    }

    return $this;
  }

  public function __toString(): string
  {
    return $this->getName().' '.$this->getSurname();
  }
}
