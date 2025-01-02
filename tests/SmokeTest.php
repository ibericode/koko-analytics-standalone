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
    public function testLoginpage(): void
    {
        $client = self::createClient();
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form button[type="submit"]');
        $this->assertSelectorExists('h1');
    }

    public function provideDashboardUrls(): \Generator
    {
        yield ['/website.com'];
        yield ['/website.com?date-start=2024-01-01&date-end=2024-12-31'];
        yield ['/website.com?date-range=today'];
        yield ['/website.com?date-range=this_week'];
        yield ['/website.com?date-range=last_week'];
        yield ['/website.com?date-range=this_month'];
        yield ['/website.com?date-range=last_month'];
        yield ['/website.com?date-range=this_year'];
        yield ['/website.com?date-range=last_year'];
    }

    /**
     * @dataProvider provideDashboardUrls
     */
    public function testUnauthenticatedUserRedirects($url) {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertResponseRedirects();
    }

    /**
     * @dataProvider provideDashboardUrls
     */
    public function testProtectedPageIsSuccessful($url): void
    {
        /** @var KernelBrowser $client */
        $client = self::createClient();
        /** @var \App\Repository\UserRepository $repo */
        $repo = self::getContainer()->get(UserRepository::class);
        $user = $repo->getByEmail('test@kokoanalytics.com');

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
        $this->assertSelectorExists('.chart');
        $this->assertSelectorExists('.totals');
        $this->assertSelectorExists('.datepicker');
        $this->assertSelectorExists('.table');
    }


}
