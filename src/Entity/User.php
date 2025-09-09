<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\FileSystem;
use App\Enum\UserRole;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(
  name: 'UNIQ_IDENTIFIER_NAME',
  fields: ['name'],
)]
#[ORM\UniqueConstraint(
  name: 'UNIQ_IDENTIFIER_EMAIL',
  fields: ['email'],
)]
#[ORM\UniqueConstraint(
  name: 'UNIQ_IDENTIFIER_NEW_EMAIL',
  fields: ['newEmail'],
)]
#[UniqueEntity(
  fields: 'name',
  message: 'unique.name',
)]
#[UniqueEntity(
  fields: 'email',
  message: 'unique.email',
)]
#[UniqueEntity(
  fields: 'newEmail',
  message: 'unique.email',
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  public const int NAME_MAX_LENGTH = 60;
  public const int EMAIL_MAX_LENGTH = 100;
  public const int ROLE_MAX_LENGTH = 60;
  public const int PHOTO_MAX_LENGTH = 60;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: "SEQUENCE")]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: self::NAME_MAX_LENGTH)]
  private ?string $name = null;

  #[ORM\Column(length: self::EMAIL_MAX_LENGTH)]
  private ?string $email = null;

  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: self::ROLE_MAX_LENGTH)]
  private UserRole $role = UserRole::User;

  #[ORM\Column]
  private bool $emailVerified = false;

  #[ORM\Column(length: self::PHOTO_MAX_LENGTH, nullable: true)]
  private ?string $photo = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $createdAt = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $passwordChangedAt = null;

  #[ORM\Column(length: self::EMAIL_MAX_LENGTH, nullable: true)]
  private ?string $newEmail = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $newEmailExpires = null;

  public function __construct() {
    $this->createdAt = new \DateTime();
  }

  public function getId(): ?int
  {
    return $this->id;
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

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): static
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   *
   * @return list<UserRole>
   */
  public function getRoles(): array
  {
    return [$this->role->value];
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  public function emailVerified(): bool
  {
    return $this->emailVerified;
  }

  public function setEmailVerified(bool $emailVerified): static
  {
    $this->emailVerified = $emailVerified;

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

  public function getCreatedAt(): ?\DateTimeInterface
  {
    return $this->createdAt;
  }

  public function getPasswordChangedAt(): ?\DateTimeInterface
  {
    return $this->passwordChangedAt;
  }

  public function setPasswordChangedAt(?\DateTimeInterface $passwordChangedAt): static
  {
    $this->passwordChangedAt = $passwordChangedAt;

    return $this;
  }

  public function getPhotosDir(): string
  {
    return FileSystem::IMAGES_DIR . "/users/" . $this->id;
  }

  public function getNewEmail(): ?string
  {
    return $this->newEmail;
  }

  public function setNewEmail(?string $newEmail): static
  {
    $this->newEmail = $newEmail;

    return $this;
  }

  public function getNewEmailExpires(): ?\DateTimeInterface
  {
    return $this->newEmailExpires;
  }

  public function setNewEmailExpires(?\DateTimeInterface $newEmailExpires): static
  {
    $this->newEmailExpires = $newEmailExpires;

    return $this;
  }

  public function setDefaultNewEmailExpires(): static
  {
    $this->newEmailExpires = new \DateTime('+2 days');

    return $this;
  }

  public function newEmailExpired(): bool
  {
    return !$this->newEmailExpires || $this->newEmailExpires < new \DateTime();
  }

  public function isNewEmailSet(): bool
  {
    return $this->newEmail && !$this->newEmailExpired();
  }

  public function getRole(): UserRole
  {
    return $this->role;
  }

  public function setRole(UserRole $role): static
  {
    $this->role = $role;

    return $this;
  }
}
