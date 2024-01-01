<?php

declare(strict_types=1);

namespace App\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(name: 'ago', callable: [$this, 'calculateTimeAgo']),
        ];
    }

    public function calculateTimeAgo(\DateTimeInterface $dateTime): string
    {
        $now = new \DateTime();
        $interval = $now->diff($dateTime);

        $years = $interval->y;
        $months = $interval->m;
        $days = $interval->d;

        return match (true) {
            0 === $years && 0 === $months && 0 === $days => 'Aujourd\'hui',
            0 === $years && 0 === $months && 1 === $days => 'Hier',
            0 === $years && 0 === $months && $days > 1 => sprintf('%d jours', $days),
            0 === $years && 1 === $months && 0 === $days => '1 mois',
            0 === $years && 1 === $months && 1 === $days => '1 mois et 1 jour',
            0 === $years && 1 === $months && $days > 1 => sprintf('1 mois et %d jours', $days),
            0 === $years && $months > 1 && 0 === $days => sprintf('%d mois', $months),
            0 === $years && $months > 1 && 1 === $days => sprintf('%d mois et 1 jour', $months),
            0 === $years && $months > 1 && $days > 1 => sprintf('%d mois et %d jours', $months, $days),
            1 === $years && 0 === $months && 0 === $days => '1 an',
            1 === $years && 0 === $months && 1 === $days => '1 an et 1 jour',
            1 === $years && 0 === $months && $days > 1 => sprintf('1 an et %d jours', $days),
            1 === $years && 1 === $months && 0 === $days => '1 an et 1 mois',
            1 === $years && 1 === $months && 1 === $days => '1 an, 1 mois et 1 jour',
            1 === $years && 1 === $months && $days > 1 => sprintf('1 an, 1 mois et %d jours', $days),
            1 === $years && $months > 1 && 0 === $days => sprintf('1 an et %d mois', $months),
            // 1 === $years && $months > 1 && 1 === $days => sprintf('1 a, %d mois et 1 jour', $months),
            // 1 === $years && $months > 1 && $days > 1 => sprintf('1 an, %d mois et %d jours', $months, $days),
            $years > 1 && 0 === $months && 0 === $days => sprintf('%d ans', $years),
            $years > 1 && 0 === $months && 1 === $days => sprintf('%d ans et 1 jour', $years),
            $years > 1 && 0 === $months && $days > 1 => sprintf('%d ans et %d jours', $years, $days),
            $years > 1 && 1 === $months && 0 === $days => sprintf('%d ans et 1 mois', $years),
            $years > 1 && 1 === $months && 1 === $days => sprintf('%d ans, 1 mois et 1 jour', $years),
            $years > 1 && 1 === $months && $days > 1 => sprintf('%d a, 1 mois et %d jours', $years, $days),
            $years > 1 && $months > 1 && 0 === $days => sprintf('%d a et %d m', $years, $months),
            $years > 1 && $months > 1 && 1 === $days => sprintf('%d a, %d m et 1 jour', $years, $months),
            $years > 1 && $months > 1 && $days > 1 => sprintf('%d a, %d m et %d j', $years, $months, $days),

            default => sprintf('%d a, %d m, %d j', $years, $months, $days),
        };
    }
}
