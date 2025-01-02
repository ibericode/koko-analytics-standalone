<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

use PDO;

// Define service as lazy so we do not connect to the database everytime this service is injected (but not used)
#[Autoconfigure(lazy: true)]
class Database extends PDO
{
    const DRIVER_SQLITE = 'sqlite';
    const DRIVER_MYSQL = 'mysql';

    private string $driverName = '';

    public function __construct(string $dsn, ?string $username = null, ?string $password = null)
    {
        $this->driverName = \substr($dsn, 0, \strpos($dsn, ':'));

        parent::__construct($this->makeDatabasePathAbsolute($dsn), $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    private function makeDatabasePathAbsolute(string $dsn): string
    {
        // do nothing if not using sqlite driver
        if (!\str_starts_with($dsn, 'sqlite:')) {
            return $dsn;
        }

        // return unmodified if already absolute
        if (\str_starts_with($dsn, 'sqlite:/') || \str_starts_with($dsn, 'sqlite::memory:')) {
            return $dsn;
        }

        $root = \dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $database = \substr($dsn, \strlen('sqlite:'));
        return "sqlite:{$root}{$database}";
    }

    public function getDriverName(): string
    {
        return $this->driverName;
    }
}

