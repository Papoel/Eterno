<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\FirstLastUserNameTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse email.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use CreatedAtTrait;
    use FirstLastUserNameTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    #[Assert\NotBlank(message: 'Veuillez saisir une adresse email.')]
    #[Assert\Length(max: 180, maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez saisir un mot de passe.')]
    #[Assert\Length(
        min: 6,
        max: 50,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $password;

    /** @var array<string> */
    #[ORM\Column]
    private array $roles = [];

    /** @var Collection<int, Light> */
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'userAccount', targetEntity: Light::class, orphanRemoval: true)]
    private Collection $lights;

    /** @var Collection<int, Message> */
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'userAccount', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }

        $this->lights = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null !== $this->username) {
            return $this->username;
        }

        if (null !== $this->firstname && null !== $this->lastname) {
            return ucfirst($this->firstname).' '.ucfirst($this->lastname);
        }

        return $this->email ?? '';
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param array<string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Light>
     */
    public function getLights(): Collection
    {
        return $this->lights;
    }

    public function addLight(Light $light): static
    {
        if (!$this->lights->contains($light)) {
            $this->lights->add($light);
            $light->setUserAccount($this);
        }

        return $this;
    }

    public function removeLight(Light $light): static
    {
        if ($this->lights->removeElement($light)) {
            // set the owning side to null (unless already changed)
            if ($light->getUserAccount() === $this) {
                $light->setUserAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUserAccount($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($message->getUserAccount() === $this) {
            $this->messages->removeElement($message);
        }

        return $this;
    }
}
