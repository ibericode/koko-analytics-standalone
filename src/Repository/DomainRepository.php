<?php

namespace App\Repository;

use App\Database;
use App\Entity\Domain;

class DomainRepository
{
    public function __construct(
        protected Database $db
    ) {
    }

    /**
     * @return Domain[]
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Domain::class);
    }

    public function getByName(string $name): ?Domain
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $obj = $stmt->fetchObject(Domain::class);
        return $obj ?: null;
    }

    public function getSettings(Domain $domain): array
    {
        $stmt = $this->db->prepare("SELECT name, value FROM koko_analytics_settings WHERE domain_id = ?");
        $stmt->execute([$domain->getId()]);
        $defaults = [
            'timezone' => 'UTC',
            'purge_treshold' => 10 * 365, // 10 years
            'excluded_ip_addresses' => "127.0.0.1\n",
        ];
        return array_merge($defaults, $stmt->fetchAll(\PDO::FETCH_UNIQUE));
    }

    public function insert(Domain $domain): void
    {
        $this->db->prepare(
            "INSERT INTO koko_analytics_domains (name) VALUES (?)"
        )->execute([$domain->getName()]);
        $domain->setId($this->db->lastInsertId());
    }

    public function delete(Domain $domain): void
    {
        $this->db->prepare(
            "DELETE FROM koko_analytics_domains WHERE id = ?"
        )->execute([$domain->getId()]);
        $domain->setId(null);
    }

    public function reset(): void
    {
        $this->db->exec("DELETE FROM koko_analytics_domains");
    }
}
