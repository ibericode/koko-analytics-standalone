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
        $domain->name = ('website.com');
        $domain->timezone = ('Europe/Amsterdam');
        $domain->purge_treshold = (100);
        $domain->excluded_ip_addresses = (['127.0.0.1']);
        $repo->insert($domain);
        self::assertGreaterThan(0, $domain->id);

        // assert repository contains 1 item now
        self::assertCount(1, $repo->getAll());

        // asert item matches what we just inserted
        $saved = $repo->getByName('website.com');
        self::assertNotNull($saved);
        self::assertEquals($domain->name, $saved->name);
        self::assertEquals($domain->timezone, $saved->timezone);
        self::assertEquals($domain->purge_treshold, $saved->purge_treshold);
        self::assertEquals($domain->excluded_ip_addresses, $saved->excluded_ip_addresses);

        // assert repository is empty again after calling reset
        $repo->reset();
        self::assertEquals([], $repo->getAll());
    }
}
