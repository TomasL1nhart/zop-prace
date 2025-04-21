<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;

final class UserFacade implements Nette\Security\Authenticator
{
    use Nette\SmartObject;

    public function __construct(
        private Nette\Database\Explorer $database,
        private Passwords $passwords,
    ) {}

    public function authenticate(string $username, string $password): Nette\Security\Identity
    {
        $row = $this->database->table('users')
            ->where('username', $username)
            ->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('Uživatel nebyl nalezen.');
        }

        if (!$this->passwords->verify($password, $row->password)) {
            throw new Nette\Security\AuthenticationException('Nesprávné heslo.');
        }

        return new Nette\Security\Identity(
            $row->id,
            $row->role ?? 'user', // výchozí role
            ['username' => $row->username, 'email' => $row->email]
        );
    }

    public function addUser(string $username, string $email, string $password): void
    {
        $existing = $this->database->table('users')
            ->where('username', $username)
            ->fetch();

        if ($existing) {
            throw new \RuntimeException('Uživatel s tímto jménem již existuje.');
        }

        $this->database->table('users')->insert([
            'username' => $username,
            'email' => $email,
            'password' => $this->passwords->hash($password),
        ]);
    }
    
}