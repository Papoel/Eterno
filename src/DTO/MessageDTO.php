<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MessageDTO
{
    #[Assert\NotBlank(message: 'Le message ne peut pas être vide')]
    #[Assert\Length(
        min: 2,
        max: 1000,
        minMessage: 'Le message doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le message ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $content = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
