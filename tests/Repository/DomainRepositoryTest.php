<?php

namespace App\Tests\Repository;

use App\Entity\Domain;
use App\Repository\DomainRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DomainRepositoryTest extends KernelTestCase
{
    /**
     *  @covers DomainRepository::getAll
     *  @covers DomainRepository::getByName
     *  @covers DomainRepository::insert
     *  @covers DomainRepository::reset
     */
    public function testClass(): void
    {
        /** @var DomainRepository */
        $repo = self::getContainer()->get(DomainRepository::class);

        // assert repository is empty after calling reset
        $repo->reset();
        self::assertEquals([], $repo->getAll());
        self::assertEquals(null, $repo->getByName('website.com'));

        // assert inserting a domain sets the ID
        $domain = new Domain();
        $domain->setName('website.com');
        $domain->setTimezone('Europe/Amsterdam');
        $domain->setPurgeTreshold(100);
        $domain->setExcludedIpAddresses(['127.0.0.1']);
        $repo->insert($domain);
        self::assertGreaterThan(0, $domain->getId());

        // assert repository contains 1 item now
        self::assertCount(1, $repo->getAll());

        // asert item matches what we just inserted
        $saved = $repo->getByName('website.com');
        self::assertNotNull($saved);
        self::assertEquals($domain->getName(), $saved->getName());
        self::assertEquals($domain->getTimezone(), $saved->getTimezone());
        self::assertEquals($domain->getPurgeTreshold(), $saved->getPurgeTreshold());
        self::assertEquals($domain->getExcludedIpAddresses(), $saved->getExcludedIpAddresses());

        // assert repository is empty again after calling reset
        $repo->reset();
        self::assertEquals([], $repo->getAll());
    }
}
