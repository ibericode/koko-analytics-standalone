<?php

namespace App;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

class Dates
{
    public function getFirstDayOfWeek(\DateTimeImmutable $dt, int $week_starts_on = 0): \DateTimeImmutable
    {
        if ((int) $dt->format('w') === $week_starts_on) {
            return $dt;
        }

        $dt = $dt->modify("last sunday, +{$week_starts_on} days");
        if ($dt === false) {
            throw new InvalidArgumentException("Could not set start of week on DateTime object");
        }
        return $dt;
    }

    public function getDateRange(string $range, \DateTimeImmutable $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC')), int $start_of_week = 0): array
    {
        switch ($range) {
            case 'today':
                return [
                    $now->modify('today midnight'),
                    $now->modify('tomorrow midnight, -1 second')
                ];
            case 'yesterday':
                return [
                    $now->modify('yesterday midnight'),
                    $now->modify('today midnight, -1 second')
                ];
            case 'this_week':
                $start = $this->getFirstDayOfWeek($now, $start_of_week);
                return [
                    $start,
                    $start->modify('+7 days, midnight, -1 second')
                ];
            case 'last_week':
                $start = $this->getFirstDayOfWeek($now, $start_of_week)->modify('-7 days');
                return [
                    $start,
                    $start->modify('+7 days, midnight, -1 second')
                ];
            case 'last_14_days':
                return [
                    $now->modify('-14 days'),
                    $now->modify('tomorrow midnight, -1 second')
                ];
            default:
            case 'last_28_days':
                return [
                    $now->modify('-28 days'),
                    $now->modify('tomorrow midnight, -1 second')
                ];
            case 'this_month':
                return [
                    $now->modify('first day of this month'),
                    $now->modify('last day of this month')
                ];
            case 'last_month':
                return [
                    $now->modify('first day of last month, midnight'),
                    $now->modify('last day of last month')
                ];
            case 'this_year':
                return [
                    $now->setDate((int) $now->format('Y'), 1, 1),
                    $now->setDate((int) $now->format('Y'), 12, 31),
                ];
            case 'last_year':
                return [
                    $now->setDate((int) $now->format('Y') - 1, 1, 1),
                    $now->setDate((int) $now->format('Y') - 1, 12, 31),
                ];
        }
    }
}
