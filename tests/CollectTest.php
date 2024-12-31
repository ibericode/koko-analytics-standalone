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
        $client->request('GET', '/collect?p=/about&v=1&pv=1');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/collect?p=/about&v=1&pv=1&r=https://www.kokoanalytics.com/');
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
        $client->request('GET', '/collect?p=/');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?v=0');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?pv=0');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?r=https://www.kokoanalytics.com');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?p=/&v=0');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?p=/&pv=0');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?v=0&pv=0');
        self::assertResponseStatusCodeSame(400);
    }


    public function testRequestWithInvalidQueryParameters(): void
    {
        $client = self::createClient();
        $client->request('GET', '/collect?p=/&v=f&pv=0');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?p=/&v=0&pv=f');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/collect?p=/&v=1&pv=1&r=not-an-url');
        self::assertResponseStatusCodeSame(400);
    }

}
