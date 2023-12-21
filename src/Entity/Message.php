<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`messages`')]
class Message
{
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1,
        max: 750,
        minMessage: 'Votre message doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Votre message ne peut pas contenir plus de {{ limit }} caractères.'
    )]
    private string $message;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private User $userAccount;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private Light $light;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getUserAccount(): ?User
    {
        return $this->userAccount;
    }

    public function setUserAccount(User $userAccount): static
    {
        $this->userAccount = $userAccount;

        return $this;
    }

    public function getLight(): ?Light
    {
        return $this->light;
    }

    public function setLight(Light $light): static
    {
        $this->light = $light;

        return $this;
    }
}
