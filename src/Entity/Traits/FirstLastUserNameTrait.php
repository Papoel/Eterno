<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait FirstLastUserNameTrait
{
    public const LENGTH_FIELD_MAX = 50;

    // define attribute columns and use const length for max length
    #[ORM\Column(length: self::LENGTH_FIELD_MAX)]
    #[Assert\NotBlank(message: 'Veuillez saisir votre prénom.')]
    #[Assert\Length(
        min: 2,
        max: self::LENGTH_FIELD_MAX,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $firstname;

    #[ORM\Column(length: self::LENGTH_FIELD_MAX, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: self::LENGTH_FIELD_MAX,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: self::LENGTH_FIELD_MAX, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: self::LENGTH_FIELD_MAX,
        minMessage: 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $username = null;

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }
}
