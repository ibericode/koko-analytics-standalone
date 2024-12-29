<?php

namespace App\Tests;

use App\Database;
use App\Repository\UserRepository;
use App\Security\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;

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
        /** @var KernelBrowser $client */
        $client = self::createClient();
        /** @var \App\Repository\UserRepository $repo */
        $repo = self::getContainer()->get(UserRepository::class);
        $user = $repo->getByEmail('hi@dvk.co');

        // authenticate user, taken from KernelBrowser::loginUser
        $session = self::getContainer()->get('session.factory')->createSession();
        $session->set('user', $user);
        $session->save();
        $domains = array_unique(array_map(fn (Cookie $cookie) => $cookie->getName() === $session->getName() ? $cookie->getDomain() : '', $client->getCookieJar()->all())) ?: [''];
        foreach ($domains as $domain) {
            $cookie = new Cookie($session->getName(), $session->getId(), null, null, $domain);
            $client->getCookieJar()->set($cookie);
        }

        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }


}
