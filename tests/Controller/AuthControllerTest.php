<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = self::createClient();

        $client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        $this->assertSelectorExists('form button[type="submit"]');
        $this->assertSelectorExists('h1');

        $client->submitForm('Log in', [
            '_username' => 'test@kokoanalytics.com',
            '_password' => '',
        ]);
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.error');

       // create test user for logging in
        $repo = self::getContainer()->get(UserRepository::class);
        $repo->reset();
        $user = new User();
        $user->setEmail('test@kokoanalytics.com');
        $user->setPassword(\password_hash('password', PASSWORD_DEFAULT));
        $repo->save($user);

        $client->submitForm('Log in', [
            '_username' => 'test@kokoanalytics.com',
            '_password' => 'password',
        ]);
        $client->followRedirects(true);
        self::assertResponseRedirects();

        $repo->reset();
    }

    public function testLogout(): void
    {
        $client = self::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/logout');
        self::assertResponseIsSuccessful();
    }
}
