<?php

namespace App\Repository;

use App\Database;
use App\Entity\Domain;
use LogicException;

class DomainRepository
{
    public function __construct(
        protected Database $db
    ) {
    }

    private function hydrate(array $data): Domain
    {
        $domain = new Domain();
        $domain->setId($data['id']);
        $domain->setName($data['name']);
        $domain->setTimezone($data['timezone']);
        $domain->setExcludedIpAddresses(array_map('trim', explode("\n", $data['excluded_ip_addresses'])));
        $domain->setPurgeTreshold($data['purge_treshold']);
        return $domain;
    }

    /**
     * @return Domain[]
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains");
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'hydrate'], $result);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Domain::class);
    }

    public function getByName(string $name): ?Domain
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_domains WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $obj = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $obj ? $this->hydrate($obj) : null;
    }

    public function update(Domain $domain): void
    {
        if (!$domain->getId()) {
            throw new LogicException("Updating non-existing domain");
        }

        $this->db->prepare(
            "UPDATE koko_analytics_domains SET name = ?, timezone = ?, purge_treshold = ?, excluded_ip_addresses = ? WHERE id = ?"
        )->execute([$domain->getName(), $domain->getTimezone(), $domain->getPurgeTreshold(), join("\n", $domain->getExcludedIpAddresses()), $domain->getId() ]);
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
