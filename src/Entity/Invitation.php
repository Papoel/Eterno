<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Repository\InvitationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
#[ORM\Table(name: 'invitations')]
class Invitation
{
    use CreatedAtTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $email;

    #[ORM\Column(type: 'uuid', nullable: false)]
    private Uuid $uuid;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    private ?User $friend = null;

    #[ORM\Column]
    private ?bool $accepted = false;

    // Create UUID when a new invitation is created

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
        $this->createdAt = new \DateTimeImmutable(datetime: 'now', timezone: $timezone);

        $this->uuid = Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getFriend(): ?User
    {
        return $this->friend;
    }

    public function setFriend(?User $friend): static
    {
        $this->friend = $friend;

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }
}
