<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
  public const int NAME_MAX_LENGTH = 100;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: "SEQUENCE")]
  #[ORM\Column]
  private ?int $id = null;

  #[Assert\NotBlank(message: 'not_blank')]
  #[Assert\Length(
    min: 2,
    max: self::NAME_MAX_LENGTH,
    minMessage: 'length.min',
    maxMessage: 'length.max',
  )]
  #[ORM\Column(length: self::NAME_MAX_LENGTH)]
  private ?string $name = null;

  #[Assert\NotBlank(message: 'not_blank')]
  #[Assert\Length(
    min: 2,
    max: self::NAME_MAX_LENGTH,
    minMessage: 'length.min',
    maxMessage: 'length.max',
  )]
  #[ORM\Column(length: self::NAME_MAX_LENGTH)]
  private ?string $surname = null;

  /** @var Collection<int, Book> */
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
