<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\FirstLastUserNameTrait;
use App\Repository\LightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LightRepository::class)]
#[ORM\Table(name: '`lights`')]
#[ORM\HasLifecycleCallbacks]
class Light
{
    use CreatedAtTrait;
    use FirstLastUserNameTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\LessThanOrEqual(propertyPath: 'deceasedAt', message: 'La date de naissance doit être antérieure à la date de décès.')]
    #[Assert\LessThanOrEqual(value: 'today', message: 'La date de naissance doit être antérieure à la date du jour.')]
    #[Assert\Date(message: 'Veuillez saisir une date de naissance valide.')]
    private ?\DateTimeInterface $birthdayAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'birthdayAt', message: 'La date de décès doit être postérieure à la date de naissance.')]
    #[Assert\LessThanOrEqual(value: 'today', message: 'La date de décès doit être antérieure ou égal à la date du jour.')]
    private ?\DateTimeInterface $deceasedAt = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBirthdayAt(): ?\DateTimeInterface
    {
        return $this->birthdayAt;
    }

    public function setBirthdayAt(?\DateTimeInterface $birthdayAt): static
    {
        $this->birthdayAt = $birthdayAt;

        return $this;
    }

    public function getDeceasedAt(): ?\DateTimeInterface
    {
        return $this->deceasedAt;
    }

    public function setDeceasedAt(\DateTimeInterface $deceasedAt): static
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
}
