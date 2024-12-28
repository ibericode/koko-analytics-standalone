<?php

namespace App;

class Database extends \PDO {
    public function __construct(string $host, string $name, string $username, string $password) {
        parent::__construct("mysql:host={$host};dbname={$name}", $username, $password);
    }
}
