<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use PDO;

class Database
{
    const DRIVER_SQLITE = 'sqlite';
    const DRIVER_MYSQL = 'mysql';

    private string $driverName;
    private ?\PDO $conn = null;

    public function __construct(
        private string $dsn,
        private ?string $username = null,
        private ?string $password = null
    ) {
        $this->driverName = \substr($dsn, 0, \strpos($dsn, ':'));
    }

    public function getConnection(): PDO
    {
        if (!$this->conn) {
            $this->conn = new \PDO($this->makeDatabasePathAbsolute($this->dsn), $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return $this->conn;
    }

    public function prepare(string $query, array $options = []): \PDOStatement|false
    {
        return $this->getConnection()->prepare($query, $options);
    }

    public function exec(string $statement): int|false
    {
        return $this->getConnection()->exec($statement);
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): \PDOStatement|false
    {
        return $this->getConnection()->query($query, $fetchMode, $fetchModeArgs);
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return $this->getConnection()->lastInsertId($name);
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
