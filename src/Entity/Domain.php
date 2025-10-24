<?php

namespace App\Entity;

class Domain
{
    public ?int $id = null;
    public string $name = '';
    public string $timezone = 'UTC';
    public array $excluded_ip_addresses = [];
    public int $purge_treshold = 5 * 365;
    public int $user_id;
}
