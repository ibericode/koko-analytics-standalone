<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

// Define service as lazy so we do not connect to the database everytime this service is injected (but not used)
#[Autoconfigure(lazy: true)]
class Database extends \PDO
{
    public function __construct(string $host, string $name, string $username, string $password)
    {
        parent::__construct("mysql:host={$host};dbname={$name}", $username, $password);
    }
}
