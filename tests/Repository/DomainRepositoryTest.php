<?php

namespace App\Tests\Repository;

use App\Entity\Domain;
use App\Repository\DomainRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DomainRepositoryTest extends KernelTestCase {
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
        $repo->insert($domain);
        self::assertGreaterThan(0, $domain->getId());

        // assert repository contains 1 item now and item is what we just inserted
        self::assertCount(1, $repo->getAll());
        self::assertEquals($domain, $repo->getByName('website.com'));

        // assert repository is empty again after calling reset
        $repo->reset();
        self::assertEquals([], $repo->getAll());
    }
}
