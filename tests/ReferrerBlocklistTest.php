<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use App\Dates;
use App\ReferrerBlocklist;

class ReferrerBlocklistTest extends TestCase
{
    public function test(): void
    {
        $blocklist = new ReferrerBlocklist;
        @unlink($blocklist->getFilename());
        self::assertEquals([], $blocklist->read(), "non-existing blocklist not empty");

        $blocklist->update();
        self::assertNotEmpty($blocklist->read(), "blocklist not updated");
    }
}
