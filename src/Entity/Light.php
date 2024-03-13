<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\FirstLastUserNameTrait;
use App\Repository\LightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: LightRepository::class)]
#[ORM\Table(name: '`lights`')]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Light
{
    use FirstLastUserNameTrait;
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $birthdayAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $deceasedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = 'default.jpg';

    #[Vich\UploadableField(mapping: 'light_photo', fileNameProperty: 'photo')]
    private ?File $photoFile = null;

    #[ORM\ManyToOne(inversedBy: 'lights')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?User $userAccount = null;

    /** @var Collection<int, Message> */
    #[ORM\OneToMany(mappedBy: 'light', targetEntity: Message::class, orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function __toString(): string
    {
        return ucfirst(string: $this->getFirstname() ?? '');
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getBirthdayAt(): ?\DateTime
    {
        return $this->birthdayAt;
    }

    public function setBirthdayAt(?\DateTime $birthdayAt): static
    {
        $this->birthdayAt = $birthdayAt;

        return $this;
    }

    public function getDeceasedAt(): ?\DateTime
    {
        return $this->deceasedAt;
    }

    public function setDeceasedAt(\DateTime $deceasedAt): static
    {
        $this->deceasedAt = $deceasedAt;

        return $this;
    }

    public function getUserAccount(): ?User
    {
        return $this->userAccount;
    }

    public function setUserAccount(?User $userAccount): static
    {
        $this->userAccount = $userAccount;

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
            $message->setLight($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($message->getLight() === $this) {
            $this->messages->removeElement($message);
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    /**
     * @throws \Exception
     */
    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;

        if (null !== $photoFile) {
            $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
            $this->updatedAt = new \DateTimeImmutable(timezone: $timezone);
        }
    }

    public function getSentMessagesCount(): int
    {
        $count = 0;

        foreach ($this->getMessages() as $message) {
            if ($message->getUserAccount() === $this->getUserAccount()) {
                ++$count;
            }
        }

        return $count;
    }

    public function getFullname(): string
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getAge(): ?int
    {
        $birthday = $this->getBirthdayAt();
        $deceased = $this->getDeceasedAt();

        if ($birthday instanceof \DateTimeInterface && $deceased instanceof \DateTimeInterface) {
            return $birthday->diff($deceased)->y;
        }

        return null;
    }
}
