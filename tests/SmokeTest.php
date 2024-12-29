<?php

namespace App\Tests;

use App\Database;
use App\Repository\UserRepository;
use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function provideUrlsWithoutAuthentication(): \Generator
    {
        yield ['/login'];
        // ...
    }

    public function provideUrlsWithAuthentication(): \Generator
    {
        yield ['/'];
        // ...
    }

    /**
     * @dataProvider provideUrlsWithoutAuthentication
     */
    public function testPageIsSuccessful($url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider provideUrlsWithAuthentication
     */
    public function testUnauthenticatedUserRedirects($url) {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertResponseRedirects();
    }

    /**
     * @dataProvider provideUrlsWithAuthentication
     */
    public function testProtectedPageIsSuccessful($url): void
    {
        $client = self::createClient();
        /** @var \App\Repository\UserRepository $db */
        $repo = self::getContainer()->get(UserRepository::class);
        $user = $repo->getByEmail('hi@dvk.co');
        $client->loginUser($user);
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }


}
