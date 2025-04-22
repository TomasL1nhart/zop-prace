<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Security\Passwords;
use Nette\Utils\Validators;

final class UserFacade
{
    public function __construct(
        private Explorer $db,
        private Passwords $passwords,
    ) {}

    public function register(string $username, string $email, string $password): void
    {
        Validators::assert($email, 'email');
        Validators::assert($username, 'string:3..20');

        if ($this->db->table('users')->where('email', $email)->fetch()) {
            throw new \Exception('Email is already registered.');
        }

        if ($this->db->table('users')->where('username', $username)->fetch()) {
            throw new \Exception('Username is already taken.');
        }

        $this->db->table('users')->insert([
            'username' => $username,
            'email' => $email,
            'password' => $this->passwords->hash($password),
            'role' => 'user',
        ]);
    }

    public function exists(string $email): bool
    {
        return (bool) $this->db->table('users')->where('email', $email)->fetch();
    }

    public function findById(int $id): ?\Nette\Database\Table\ActiveRow
    {
        return $this->db->table('users')->get($id);
    }

    public function findByUsername(string $username): ?\Nette\Database\Table\ActiveRow
    {
        return $this->db->table('users')->where('username', $username)->fetch();
    }
}