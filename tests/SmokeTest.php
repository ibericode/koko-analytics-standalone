<?php

namespace App\Tests;

use App\Entity\Domain;
use App\Entity\User;
use App\Repository\DomainRepository;
use App\Repository\StatRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

class SmokeTest extends WebTestCase
{
    public function provideDashboardUrls(): \Generator
    {
        yield ['/smoke-test.com'];
        yield ['/smoke-test.com?date-start=2024-01-01&date-end=2024-12-31'];
        yield ['/smoke-test.com?date-range=today'];
        yield ['/smoke-test.com?date-range=this_week'];
        yield ['/smoke-test.com?date-range=last_week'];
        yield ['/smoke-test.com?date-range=this_month'];
        yield ['/smoke-test.com?date-range=last_month'];
        yield ['/smoke-test.com?date-range=this_year'];
        yield ['/smoke-test.com?date-range=last_year'];
    }

    /**
     * @dataProvider provideDashboardUrls
     */
    public function testUnauthenticatedUserRedirects($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertResponseRedirects();
    }

    /**
     * @dataProvider provideDashboardUrls
     */
    public function testProtectedPageIsSuccessful($url): void
    {
        /** @var KernelBrowser */
        $client = self::createClient();

        /** @var \App\Repository\UserRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $userRepository->reset();
        $user = new User();
        $user->setEmail('test@kokoanalytics.com');
        $user->setPassword('');
        $userRepository->save($user);

        /** @var DomainRepository */
        $domainRepository = self::getContainer()->get(DomainRepository::class);
        $domainRepository->reset();

        $domain = new Domain();
        $domain->user_id = $user->getId();
        $domain->name = 'smoke-test.com';
        $domainRepository->insert($domain);

        /** @var StatRepository */
        $statRepository = self::getContainer()->get(StatRepository::class);
        $statRepository->createTables($domain);

        /** @var Session */
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
        $this->assertSelectorExists('.table');
    }
}
