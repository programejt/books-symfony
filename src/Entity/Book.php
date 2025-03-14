<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\FileSystem;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'books')]
#[UniqueEntity(fields: 'isbn', message: 'unique.isbn')]
class Book
{
  public const int TITLE_MAX_LENGTH = 120;
  public const int DESCRIPTION_MAX_LENGTH = 3000;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: "SEQUENCE")]
  #[ORM\Column]
  private ?int $id = null;

  #[Assert\NotBlank(message: 'not_blank')]
  #[Assert\Length(
    min: 3,
    max: self::TITLE_MAX_LENGTH,
    minMessage: 'length.min',
    maxMessage: 'length.max',
  )]
  #[ORM\Column(length: self::TITLE_MAX_LENGTH)]
  private ?string $title = null;

  #[Assert\NotBlank(message: 'not_blank')]
  #[Assert\Length(
    min: 3,
    max: self::DESCRIPTION_MAX_LENGTH,
    minMessage: 'length.min',
    maxMessage: 'length.max',
  )]
  #[ORM\Column(length: self::DESCRIPTION_MAX_LENGTH)]
  private ?string $description = null;

  #[Assert\NotBlank(message: 'not_blank')]
  #[Assert\LessThan(2100, message: 'less_than')]
  #[Assert\GreaterThan(1400, message: 'greater_than')]
  #[ORM\Column(type: Types::SMALLINT)]
  private ?int $year = null;

  #[Assert\NotBlank(message: 'not_blank')]
  #[Assert\Isbn(type: Assert\Isbn::ISBN_13, message: 'isbn13')]
  #[ORM\Column(type: Types::BIGINT, unique: true)]
  private ?int $isbn = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $photo = null;

  /** @var Collection<int, Author> */
  #[Assert\Count(
    min: 1,
    max: 6,
    minMessage: 'authors.min',
    maxMessage: 'authors.max',
  )]
  #[ORM\ManyToMany(
    targetEntity: Author::class,
    inversedBy: 'books',
    cascade: ['persist'],
  )]
  #[ORM\JoinTable(name: "author_book")]
  #[ORM\JoinColumn(
    name: "book_id",
    referencedColumnName: "id",
    onDelete: 'cascade',
  )]
  #[ORM\InverseJoinColumn(
    name: "author_id",
    referencedColumnName: "id",
    onDelete: 'cascade',
  )]
  private Collection $authors;

  public function __construct()
  {
    $this->authors = new ArrayCollection();
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

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): static
  {
    $this->description = $description;

    return $this;
  }

  public function getYear(): ?int
  {
    return $this->year;
  }

  public function setYear(int $year): static
  {
    $this->year = $year;

    return $this;
  }

  public function getIsbn(): ?int
  {
    return $this->isbn;
  }

  public function setIsbn(int $isbn): static
  {
    $this->isbn = $isbn;

    return $this;
  }

  public function getPhoto(): ?string
  {
    return $this->photo;
  }

  public function setPhoto(?string $photo): static
  {
    $this->photo = $photo;

    return $this;
  }

  public function getPhotosDir(): string
  {
    return FileSystem::IMAGES_DIR . "/books/" . $this->id;
  }

  public function getPhotoPath(): string
  {
    return $this->getPhotosDir().'/'.$this->photo;
  }

  /**
   * @return Collection<int, Author>
   */
  public function getAuthors(): Collection
  {
    return $this->authors;
  }

  public function getAuthorsNames(): string
  {
    $names = '';
    $separator = ', ';

    foreach ($this->getAuthors() as $author) {
      $names .= $author.$separator;
    }

    return \rtrim($names, $separator);
  }

  public function addAuthor(Author $author): static
  {
    if (!$this->authors->contains($author)) {
      $this->authors->add($author);
      // $author->addBook($this);
    }

    return $this;
  }

  public function removeAuthor(Author $author): static
  {
    if ($this->authors->removeElement($author)) {
      // $author->removeBook($this);
    }

    return $this;
  }
}
