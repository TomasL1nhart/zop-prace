<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Security\Authenticator as NetteAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\Security\AuthenticationException;

class Authenticator implements NetteAuthenticator
{
    public function __construct(
        private Explorer $db,
        private Passwords $passwords,
    ) {}

    public function authenticate(string $email, string $password): Identity
    {
        $row = $this->db->table('users')->where('email', $email)->fetch();

        if (!$row || !$this->passwords->verify($password, $row->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return new Identity($row->id, [], ['email' => $row->email]);
    }
}