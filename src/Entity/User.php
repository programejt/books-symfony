<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\FileSystem;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', fields: ['name'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['name'], message: 'There is already an account with this name')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: "SEQUENCE")]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180)]
  private ?string $email = null;

  /**
   * @var list<string> The user roles
   */
  #[ORM\Column]
  private array $roles = [];

  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: 60)]
  private ?string $name = null;

  #[ORM\Column]
  private bool $emailVerified = false;

  #[ORM\Column(length: 60, nullable: true)]
  private ?string $photo = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $createdAt = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $passwordChangedAt = null;

  #[ORM\Column(length: 100, nullable: true)]
  private ?string $newEmail = null;

  public function __construct() {
    $this->createdAt = new \DateTime();
  }

  public function getId(): ?int
  {
    return $this->id;
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
   * @return list<string>
   */
  public function getRoles(): array
  {
    $roles = $this->roles;

    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  /**
   * @param list<string> $roles
   */
  public function setRoles(array $roles): static
  {
    $this->roles = $roles;
    return $this;
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

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;
    return $this;
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
}
