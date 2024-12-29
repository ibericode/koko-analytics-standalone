<?php

namespace App\Repository;

use App\Database;
use App\Security\User;

class UserRepository {
    public function __construct(
        protected Database $db
    ) {}

    public function getByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_users WHERE email = :email LIMIT 1;");
        $stmt->execute(["email" => $email]);
        $obj = $stmt->fetchObject(User::class);
        return $obj ?? null;
    }

}
