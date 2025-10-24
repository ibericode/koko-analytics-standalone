<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /**
     *  @covers UserRepository::getByName
     *  @covers DomainRepository::save
     *  @covers DomainRepository::reset
     */
    public function testClass(): void
    {
        /** @var UserRepository */
        $repo = self::getContainer()->get(UserRepository::class);

        $repo->reset();
        self::assertEquals(null, $repo->getByEmail('test@kokoanalytics.com'));

        $user = new User();
        $user->setEmail('test@kokoanalytics.com');
        $user->setPassword('');
        $repo->save($user);
        self::assertGreaterThan(0, $user->getId());
        self::assertEquals($user, $repo->getByEmail('test@kokoanalytics.com'));

        $repo->reset();
    }
}
