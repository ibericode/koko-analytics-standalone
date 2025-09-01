<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use App\Dates;
use App\Normalizer;

class NormalizerTest extends TestCase
{
    public function testPath(): void
    {
        $tests = [
            '' => '',
            '/' => '/',
            '/ABOUT' => '/about',
            '/about/amp/' => '/about/',
            '/about/?utm_source=source&utm_campaign=campaign&utm_medium=medium' => '/about/',
            '/about/?p=100' => '/about/?p=100',
        ];

        $normalizer = new Normalizer();
        foreach ($tests as $input => $expected) {
            self::assertEquals($expected, $normalizer->path($input));
        }
    }

    public function testReferrer(): void
    {
        $tests = [
            '' => '',
            'https://website.com/foo' => 'website.com',
            'not an url' => '',
            'https://www.google.com' => 'google.com',
        ];

        $normalizer = new Normalizer();
        foreach ($tests as $input => $expected) {
            self::assertEquals($expected, $normalizer->referrer($input));
        }
    }
}
