<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;

class CollectTest extends WebTestCase
{
    public function testValidRequest(): void
    {
        $client = self::createClient();
        $client->request('GET', '/collect?p=/about');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/collect?p=/about&r=https://www.kokoanalytics.com/');
        self::assertResponseIsSuccessful();
    }

    public function testRequestWithoutQueryParameters(): void
    {
        $client = self::createClient();
        $client->request('GET', '/collect');
        self::assertResponseStatusCodeSame(400);
    }

    public function testRequestWithMissingQueryParameters(): void
    {
        $client = self::createClient();
        $client->request('GET', '/collect?r=https://www.kokoanalytics.com');
        self::assertResponseStatusCodeSame(400);
    }


    public function testRequestWithInvalidQueryParameters(): void
    {
        $client = self::createClient();
        $client->request('GET', '/collect?p=/&r=not-an-url');
        self::assertResponseStatusCodeSame(400);
    }

}
