<?php

namespace App\Entity;

class Domain
{
    protected ?int $id = null;
    protected string $name = '';
    protected string $timezone = 'UTC';
    protected array $excluded_ip_addresses = [];
    protected int $purge_treshold = 5 * 365;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setExcludedIpAddresses(array $excluded_ip_addresses): static
    {
        $this->excluded_ip_addresses = $excluded_ip_addresses;
        return $this;
    }

    public function getExcludedIpAddresses(): array
    {
        return $this->excluded_ip_addresses;
    }

    public function setPurgeTreshold(int $purge_treshold): static
    {
        $this->purge_treshold = $purge_treshold;
        return $this;
    }

    public function getPurgeTreshold(): int
    {
        return $this->purge_treshold;
    }
}
