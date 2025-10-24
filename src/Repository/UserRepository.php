<?php

namespace App\Repository;

use App\Database;
use App\Entity\User;

class UserRepository
{
    public function __construct(
        protected Database $db
    ) {
    }

    private function hydrate(array $data): User
    {
        $user = new User();
        $user->setId((int) $data['id']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setRole($data['role']);
        return $user;
    }

    public function getByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_users WHERE email = ? LIMIT 1");
        $stmt->execute([ $email ]);
        $obj = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $obj ? $this->hydrate($obj) : null;
    }

    public function save(User $user): void
    {
        $this->db
            ->prepare("INSERT INTO koko_analytics_users (email, password) VALUES (?, ?)")
            ->execute([ $user->getEmail(), $user->getPassword() ]);
        $user->setId((int) $this->db->lastInsertId());
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
