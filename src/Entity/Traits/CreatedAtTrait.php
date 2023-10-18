<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait CreatedAtTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'La date de création ne peut pas être vide.')]
    #[Assert\Type(type: \DateTimeImmutable::class, message: 'La date de création doit être une date valide.')]
    #[Assert\LessThanOrEqual(propertyPath: 'today', message: 'La date de création ne peut pas être supérieure à la date du jour.')]
    private \DateTimeImmutable $createdAt;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @throws \Exception
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
        $this->createdAt = new \DateTimeImmutable(datetime: 'now', timezone: $timezone);
    }
}
