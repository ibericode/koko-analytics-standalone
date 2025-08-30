<?php

namespace App\Tests;

use App\SessionManager;
use PHPUnit\Framework\TestCase;

class SessionManagerTest extends TestCase
{
    public function testGenerateId(): void
    {
        $s = new SessionManager();
        $user_agent = "Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0";
        $ip_address = '127.0.0.1';
        $domain = 'website.com';
        $a = $s->generateId($domain, $user_agent, $ip_address);
        self::assertNotEmpty($a);

        $b = $s->generateId($domain, $user_agent, $ip_address);
        self::assertEquals($a, $b);

        // assert that id changes after rotating seed
        $s->rotateSeed();
        $c = $s->generateId($domain, $user_agent, $ip_address);
        self::assertNotEquals($b, $c);
    }

    public function testRotateSeed(): void
    {
        $s = new SessionManager();
        $seed_a = $s->getSeed();

        $s->rotateSeed();
        $seed_b = $s->getSeed();
        self::assertNotEquals($seed_a, $seed_b);
    }

    public function testGetSeed(): void
    {
        $s = new SessionManager();
        $seed = $s->getSeed();
        self::assertNotEmpty($seed);
    }

    public function testGetVisitedPages(): void
    {
        $s = new SessionManager();
        $user_agent = "Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0";
        $ip_address = '127.0.0.1';
        $domain = 'website.com';
        $id = $s->generateId($domain, $user_agent, $ip_address);

        self::assertEquals([], $s->getVisitedPages($id));
    }

    public function testAddVisitedPage(): void
    {
        $s = new SessionManager();
        $user_agent = "Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0";
        $ip_address = '127.0.0.1';
        $domain = 'website.com';
        $id = $s->generateId($domain, $user_agent, $ip_address);
        $s->addVisitedPage($id, '/about');
        self::assertEquals(["/about"], $s->getVisitedPages($id));
    }

    public function testPurge(): void
    {
        $s = new SessionManager();
        $s->purge();

        // we're not actually testing anything here
        // but still make sure the method above gets exercised
        self::assertTrue(true);
    }
}
