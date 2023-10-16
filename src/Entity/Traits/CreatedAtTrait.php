<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
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
