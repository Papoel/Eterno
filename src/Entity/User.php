<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\FirstLastUserNameTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse email.')]
#[UniqueEntity(fields: ['username'], message: 'Ce nom d\'utilisateur est déjà utilisé.')]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use FirstLastUserNameTrait;
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    #[Assert\NotBlank(message: 'Veuillez saisir une adresse email.')]
    #[Assert\Length(max: 180, maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez saisir un mot de passe.')]
    #[Assert\Length(
        min: 6,
        max: 70,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $password;

    /** @var array<string> */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $birthday = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Length(max: 10, maxMessage: 'Votre numéro de téléphone ne peut pas dépasser {{ limit }} chiffres.')]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[Vich\UploadableField(mapping: 'user_avatar', fileNameProperty: 'avatar')]
    private ?File $avatarFile = null;

    /** @var Collection<int, Light> */
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'userAccount', targetEntity: Light::class, orphanRemoval: true)]
    private Collection $lights;

    /** @var Collection<int, Message> */
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'userAccount', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    /** @var Collection<int, Invitation> */
    #[ORM\OneToMany(mappedBy: 'friend', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $invitations;

    public function __construct()
    {
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }

        $this->lights = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFullname().' - ('.$this->email.')';
    }

    public function getFullname(): string
    {
        $firstname = $this->firstname ?? '';
        $lastname = $this->lastname ?? '';

        return ucfirst($firstname).' '.ucfirst($lastname);
    }

    public function getId(): ?Uuid
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
        if (null === $this->email || '' === $this->email) {
            throw new \RuntimeException('User email cannot be empty');
        }

        return $this->email;
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

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTime $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
            $this->updatedAt = new \DateTimeImmutable(timezone: $timezone);
        }
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'birthday' => $this->birthday,
            'mobile' => $this->mobile,
            'avatar' => $this->avatar,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] instanceof Uuid ? $data['id'] : null;
        $this->email = is_string($data['email']) ? $data['email'] : null;
        $this->password = is_string($data['password']) ? $data['password'] : '';
        /** @var array<string> $roles */
        $roles = is_array($data['roles']) ? $data['roles'] : [];
        $this->roles = $roles;
        $this->birthday = $data['birthday'] instanceof \DateTime ? $data['birthday'] : null;
        $this->mobile = is_string($data['mobile']) ? $data['mobile'] : null;
        $this->avatar = is_string($data['avatar']) ? $data['avatar'] : null;
        $this->firstname = is_string($data['firstname']) ? $data['firstname'] : '';
        $this->lastname = is_string($data['lastname']) ? $data['lastname'] : null;
        $this->username = is_string($data['username']) ? $data['username'] : null;
        $this->createdAt = $data['createdAt'] instanceof \DateTimeImmutable ? $data['createdAt'] : new \DateTimeImmutable();
        $this->updatedAt = $data['updatedAt'] instanceof \DateTimeImmutable ? $data['updatedAt'] : null;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setFriend($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getFriend() === $this) {
                $invitation->setFriend(null);
            }
        }

        return $this;
    }
}
