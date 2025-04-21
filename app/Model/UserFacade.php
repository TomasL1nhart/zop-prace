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

    public function register(string $email, string $password): void
    {
        Validators::assert($email, 'email');

        if ($this->db->table('users')->where('email', $email)->fetch()) {
            throw new \Exception('Email is already registered.');
        }

        $this->db->table('users')->insert([
            'email' => $email,
            'password' => $this->passwords->hash($password),
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
}
