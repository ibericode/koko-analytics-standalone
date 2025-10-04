<?php

namespace App;

use PDO;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

#[Lazy]
class Database extends PDO
{
    const DRIVER_SQLITE = 'sqlite';
    const DRIVER_MYSQL = 'mysql';

    private string $driverName;

    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null
    ) {
        $this->driverName = \substr($dsn, 0, \strpos($dsn, ':'));

        parent::__construct($this->makeDatabasePathAbsolute($dsn), $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
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
