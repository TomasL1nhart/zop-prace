<?php

namespace App\Model;

use Nette\Security\Authenticator as NetteAuthenticator;
use Nette\Security\Identity;
use Nette\Security\AuthenticationException;

final class Authenticator implements NetteAuthenticator
{
    public function __construct(
        private UserFacade $userFacade,
    ) {}

    public function authenticate(string $username, string $password): Identity
    {
        $user = $this->userFacade->findByUsername($username);

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        if (!password_verify($password, $user['password'])) {
            throw new AuthenticationException('Invalid password.');
        }
        $this->userFacade->updateLastLogin($user['id']);

        return new Identity($user['id'], $user['role'], [
            'username' => $user['username'],
            'email' => $user['email'],
            'created_at' => $user['created_at'],
            'last_login' => $user['last_login'],
        ]);
    }
}
