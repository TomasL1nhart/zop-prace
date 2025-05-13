<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Utils\Validators;
use Nette\Security\Passwords;

final class UserFacade
{
    public function __construct(
        private Explorer $db,
        private Passwords $passwords,
    ) {}

    public function register(string $username, string $email, string $password): void
    {
        if (!Validators::isEmail($email)) {
            throw new \InvalidArgumentException('Neplatná emailová adresa.');
        }

        if ($this->findByUsername($username)) {
            throw new \RuntimeException('Tento username již existuje.');
        }

        if ($this->findByEmail($email)) {
            throw new \RuntimeException('Email už je zaregistrovaný.');
        }

        $this->db->table('users')->insert([
            'username' => $username,
            'email' => $email,
            'password' => $this->passwords->hash($password),
            'role' => 'user', // default role
        ]);
    }

    public function findByUsername(string $username): ?array
    {
        $row = $this->db->table('users')->where('username', $username)->fetch();
        return $row ? $row->toArray() : null;
    }

    public function findByEmail(string $email): ?array
    {
        $row = $this->db->table('users')->where('email', $email)->fetch();
        return $row ? $row->toArray() : null;
    }

    public function updateLastLogin(int $userId): void
    {
    $this->db->table('users')->where('id', $userId)->update([
        'last_login' => new \DateTime(),
    ]);
    }

    public function changePassword(int $userId, string $newPassword): void
    {
    $this->db->table('users')
        ->where('id', $userId)
        ->update([
            'password' => $this->passwords->hash($newPassword),
        ]);
    }

    public function getAll(): array
    {
    return $this->db->table('users')->fetchAll();
    }

    public function deleteUser(int $id): void
    {   
    $this->db->table('users')->where('id', $id)->delete();
    }

}