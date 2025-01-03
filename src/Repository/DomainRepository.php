<?php

namespace App\Repository;

use App\Database;
use App\Entity\Domain;

class DomainRepository {
    public function __construct(
        protected Database $db
    ) {}

    /**
     * @return Domain[]
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains;");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Domain::class);
    }

    public function getByName(string $name): ?Domain
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains WHERE name = :name LIMIT 1;");
        $stmt->execute(["name" => $name]);
        $obj = $stmt->fetchObject(Domain::class);
        return $obj ?: null;
    }
}
