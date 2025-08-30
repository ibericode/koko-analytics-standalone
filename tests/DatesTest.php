<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use App\Dates;

class DatesTest extends TestCase
{
    public function testGetFirstDayOfCurrentWeekWithWeekStartOnSunday(): void
    {
        $i = new Dates();
        self::assertEquals(new \DateTimeImmutable('2025-01-05'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-05'), 0));
        self::assertEquals(new \DateTimeImmutable('2025-01-05'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-06'), 0));
        self::assertEquals(new \DateTimeImmutable('2025-01-05'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-07'), 0));
        self::assertEquals(new \DateTimeImmutable('2025-01-05'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-08'), 0));
        self::assertEquals(new \DateTimeImmutable('2025-01-05'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-09'), 0));
        self::assertEquals(new \DateTimeImmutable('2025-01-05'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-10'), 0));
        self::assertEquals(new \DateTimeImmutable('2025-01-05'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-11'), 0));
        self::assertEquals(new \DateTimeImmutable('2025-01-12'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-12'), 0));
    }

    public function testGetFirstDayOfCurrentWeekWithWeekStartOnMonday(): void
    {
        $i = new Dates();
        self::assertEquals(new \DateTimeImmutable('2024-12-30'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-05'), 1));
        self::assertEquals(new \DateTimeImmutable('2025-01-06'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-06'), 1));
        self::assertEquals(new \DateTimeImmutable('2025-01-06'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-07'), 1));
        self::assertEquals(new \DateTimeImmutable('2025-01-06'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-08'), 1));
        self::assertEquals(new \DateTimeImmutable('2025-01-06'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-09'), 1));
        self::assertEquals(new \DateTimeImmutable('2025-01-06'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-10'), 1));
        self::assertEquals(new \DateTimeImmutable('2025-01-06'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-11'), 1));
        self::assertEquals(new \DateTimeImmutable('2025-01-06'), $i->getFirstDayOfWeek(new DateTimeImmutable('2025-01-12'), 1));
    }

    public function testGetDatesForRangeThisWeekWithWeekStartOnSunday(): void
    {
        $i = new Dates();
        self::assertEquals([new DateTimeImmutable('2025-01-05'), new DateTimeImmutable('2025-01-11 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-05'), 0));
        self::assertEquals([new DateTimeImmutable('2025-01-05'), new DateTimeImmutable('2025-01-11 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-06'), 0));
        self::assertEquals([new DateTimeImmutable('2025-01-05'), new DateTimeImmutable('2025-01-11 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-07'), 0));
        self::assertEquals([new DateTimeImmutable('2025-01-05'), new DateTimeImmutable('2025-01-11 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-08'), 0));
        self::assertEquals([new DateTimeImmutable('2025-01-05'), new DateTimeImmutable('2025-01-11 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-09'), 0));
        self::assertEquals([new DateTimeImmutable('2025-01-05'), new DateTimeImmutable('2025-01-11 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-10'), 0));
        self::assertEquals([new DateTimeImmutable('2025-01-05'), new DateTimeImmutable('2025-01-11 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-11'), 0));
        self::assertEquals([new DateTimeImmutable('2025-01-12'), new DateTimeImmutable('2025-01-18 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-12'), 0));
    }

    public function testGetDatesForRangeThisWeekWithWeekStartOnMonday(): void
    {
        $i = new Dates();
        self::assertEquals([new DateTimeImmutable('2024-12-30'), new DateTimeImmutable('2025-01-05 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-05'), 1));
        self::assertEquals([new DateTimeImmutable('2025-01-06'), new DateTimeImmutable('2025-01-12 23:59:59')], $i->getDateRange('this_week', new DateTimeImmutable('2025-01-06'), 1));
    }

    public function testGetDatesForRangeLastWeekWithWeekStartOnSunday(): void
    {
        $i = new Dates();
        self::assertEquals([new DateTimeImmutable('2024-12-29'), new DateTimeImmutable('2025-01-04 23:59:59')], $i->getDateRange('last_week', new DateTimeImmutable('2025-01-05'), 0));
        self::assertEquals([new DateTimeImmutable('2024-12-29'), new DateTimeImmutable('2025-01-04 23:59:59')], $i->getDateRange('last_week', new DateTimeImmutable('2025-01-06'), 0));
    }

    public function testGetDatesForRangeLastWeekWithWeekStartOnMonday(): void
    {
        $i = new Dates();
        self::assertEquals([new DateTimeImmutable('2024-12-23'), new DateTimeImmutable('2024-12-29 23:59:59')], $i->getDateRange('last_week', new DateTimeImmutable('2025-01-05'), 1));
        self::assertEquals([new DateTimeImmutable('2024-12-30'), new DateTimeImmutable('2025-01-05 23:59:59')], $i->getDateRange('last_week', new DateTimeImmutable('2025-01-06'), 1));
    }
}
