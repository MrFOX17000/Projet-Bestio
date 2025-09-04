<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cette adresse e-mail')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email(message: 'L\'adresse e-mail "{{ value }}" n\'est pas valide.')]
    #[Assert\NotBlank(message: 'Veuillez entrer une adresse e-mail.')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Length(
        min: 6,
        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
        // max length allowed by Symfony for security reasons
        max: 4096,
    )]
    private ?string $newPassword = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez entrer un nom d\'utilisateur.')]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: 'L\'URL "{{ value }}" n\'est pas valide.')]
    private ?string $photo = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $ecrire;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $poser;

    public function __construct()
    {
        $this->ecrire = new ArrayCollection();
        $this->poser = new ArrayCollection();
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
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
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

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): static
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getEcrire(): Collection
    {
        return $this->ecrire;
    }

    public function addEcrire(Commentaire $ecrire): static
    {
        if (!$this->ecrire->contains($ecrire)) {
            $this->ecrire->add($ecrire);
            $ecrire->setAuthor($this);
        }

        return $this;
    }

    public function removeEcrire(Commentaire $ecrire): static
    {
        if ($this->ecrire->removeElement($ecrire)) {
            // set the owning side to null (unless already changed)
            if ($ecrire->getAuthor() === $this) {
                $ecrire->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getPoser(): Collection
    {
        return $this->poser;
    }

    public function addPoser(Question $poser): static
    {
        if (!$this->poser->contains($poser)) {
            $this->poser->add($poser);
            $poser->setAuthor($this);
        }

        return $this;
    }

    public function removePoser(Question $poser): static
    {
        if ($this->poser->removeElement($poser)) {
            // set the owning side to null (unless already changed)
            if ($poser->getAuthor() === $this) {
                $poser->setAuthor(null);
            }
        }

        return $this;
    }
}
