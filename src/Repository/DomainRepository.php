<?php

namespace App\Repository;

use App\Database;
use App\Entity\Domain;

class DomainRepository {
    public function __construct(
        protected Database $db
    ) {}

    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains;");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Domain::class);
    }

    public function getByDomain(string $domain): ?Domain
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains WHERE domain = :domain LIMIT 1;");
        $stmt->execute(["domain" => $domain]);
        $obj = $stmt->fetchObject(Domain::class);
        return $obj ?: null;
    }
}
