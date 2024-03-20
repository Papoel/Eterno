<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 70,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 70,
        minMessage: 'Le sujet doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le sujet ne peut pas dépasser {{ limit }} caractères.'
    )]
    public string $subject = '';

    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.'
    )]
    public string $email = '';

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 1000,
        minMessage: 'Le message doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le message ne peut pas dépasser {{ limit }} caractères.'
    )]
    public string $message = '';
}
