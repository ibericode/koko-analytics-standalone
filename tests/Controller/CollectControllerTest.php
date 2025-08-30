<?php

namespace App\Tests\Controller;

use App\Aggregator;
use App\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;

class CollectControllerTest extends WebTestCase
{
    public function testValidRequest(): void
    {
        $client = self::createClient();
        $client->request('GET', '/collect?d=website.com&p=/about');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/collect?d=website.com&p=/about&r=https://www.kokoanalytics.com/');
        self::assertResponseIsSuccessful();
    }

    public function testRequestWithoutQueryParameters(): void
    {
        $client = self::createClient();
        $client->request('GET', '/collect');
        self::assertResponseStatusCodeSame(200);
    }

    public function provideMissingQueryParameters(): \Generator
    {
        yield ['/collect?r=https://www.kokoanalytics.com'];
        yield ['/collect?p=/r=https://www.kokoanalytics.com'];
        yield ['/collect?d=website.com&r=https://www.kokoanalytics.com'];
    }

    /**
     * @dataProvider provideMissingQueryParameters
     */
    public function testRequestWithMissingQueryParameters(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(200);
    }


    public function provideInvalidQueryParameters(): \Generator
    {
        yield ['/collect?d=website.com&p=/&r=not-an-url'];
        yield ['/collect?d=unexisting-domain.com&p=/'];
        yield ['/collect?d=../&p=/'];
    }

    /**
     * @dataProvider provideInvalidQueryParameters
     */
    public function testRequestWithInvalidQueryParameters(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(200);
    }
}
