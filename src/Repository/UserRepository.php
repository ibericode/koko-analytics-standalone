<?php

namespace App\Repository;

use App\Database;
use App\Security\User;

class UserRepository
{
    public function __construct(
        protected Database $db
    ) {
    }

    public function getByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_users WHERE email = ? LIMIT 1");
        $stmt->execute([ $email ]);
        return $stmt->fetchObject(User::class) ?: null;
    }

    public function save(User $user): void
    {
        $this->db
            ->prepare("INSERT INTO koko_analytics_users (email, password) VALUES (?, ?)")
            ->execute([ $user->getEmail(), $user->getPassword() ]);
        $user->setId($this->db->lastInsertId());
    }

    public function delete(User $user): void
    {
        $this->db
            ->prepare("DELETE FROM koko_analytics_users WHERE id = ?")
            ->execute([$user->getId()]);
        $user->setId(null);
    }

    public function reset(): void
    {
        $this->db->exec("DELETE FROM koko_analytics_users");
    }
}
