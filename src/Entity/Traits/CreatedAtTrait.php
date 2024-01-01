<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @throws \Exception
     */
    #[ORM\PrePersist]
    public function createdTimestamps(): void
    {
        $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
        $this->setCreatedAt(new \DateTimeImmutable(datetime: 'now', timezone: $timezone));
    }
}
