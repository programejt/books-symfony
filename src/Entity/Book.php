<?php

namespace App\Entity;

use App\Repository\BooksRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BooksRepository::class)]
#[ORM\Table(name: 'books')]
class Book
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[Assert\Length(
    min: 3,
    max: 255
  )]
  #[ORM\Column(length: 255)]
  private ?string $title = null;

  #[Assert\Length(
    min: 3,
    max: 255
  )]
  #[ORM\Column(length: 255)]
  private ?string $author = null;

  #[Assert\Length(
    min: 3,
    max: 255
  )]
  #[ORM\Column(length: 255)]
  private ?string $description = null;

  #[Assert\LessThan(2100)]
  #[Assert\GreaterThan(1400)]
  #[ORM\Column(type: Types::SMALLINT)]
  private ?int $year = null;

  #[Assert\Isbn(
    type: Assert\Isbn::ISBN_13,
    message: 'ISBN is not valid.'
  )]
  #[ORM\Column(type: Types::BIGINT)]
  private ?int $isbn = null;

  #[Assert\Image(
    maxSize: '5m',
    mimeTypes: [
      'image/jpg',
      'image/jpeg',
      'image/png',
      'image/webp',
      'image/avif',
      'image/heif'
    ],
    mimeTypesMessage: 'Please upload a valid image'
  )]
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $photo = null;

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

  public function getAuthor(): ?string
  {
    return $this->author;
  }

  public function setAuthor(string $author): static
  {
    $this->author = $author;
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

  public function getPhotosDir(): string {
    return "assets/images/books/".$this->id;
  }

  public function getSystemPhotosDir(): string {
    $path = $_SERVER['DOCUMENT_ROOT'];
    if (substr($path, -1) !== '/') {
      $path .= '/';
    }
    return $path.$this->getPhotosDir();
  }
}
