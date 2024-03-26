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

        if ($years > 0) {
            return $this->formatYearsMonthsDays($years, $months, $days);
        }

        if ($months > 0) {
            return $this->formatMonthsDays($months, $days);
        }

        if (0 === $days) {
            return 'Aujourd\'hui';
        }

        if (1 === $days) {
            return 'Hier';
        }

        return $this->formatDays($days);
    }

    private function formatYearsMonthsDays(int $years, int $months, int $days): string
    {
        $parts = [];

        if ($years > 0) {
            $parts[] = sprintf('%d an%s', $years, $years > 1 ? 's' : '');
        }

        if ($months > 0) {
            $parts[] = sprintf('%d mois', $months);
        }

        if ($days > 0) {
            $parts[] = sprintf('%d jour%s', $days, $days > 1 ? 's' : '');
        }

        return implode(' ', $parts);
    }

    private function formatMonthsDays(int $months, int $days): string
    {
        $parts = [];

        if ($months > 0) {
            $parts[] = sprintf('%d mois', $months);
        }

        if ($days > 0) {
            $parts[] = sprintf('%d jour%s', $days, $days > 1 ? 's' : '');
        }

        return implode(' ', $parts);
    }

    private function formatDays(int $days): string
    {
        return sprintf('%d jour%s', $days, $days > 1 ? 's' : '');
    }
}
