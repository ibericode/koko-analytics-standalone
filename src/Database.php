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
        $this->driverName = substr($dsn, 0, strpos($dsn, ':'));

        // create absolute path from project root dir if we're given a relative path in dsn
        // TODO: Write tests for this
        if ($this->driverName === self::DRIVER_SQLITE) {
            $database = substr($dsn, strlen($this->driverName) + 1);
            if ($database[0] !== ':' && $database[0] !== '/') {
                $database = __DIR__ . '/../' . $database;
                $dsn = $this->driverName . ':' . $database;
            }
        }

        parent::__construct($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getDriverName(): string
    {
        return $this->driverName;
    }
}

