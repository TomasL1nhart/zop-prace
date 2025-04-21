<?php

namespace App\Security;

use Nette\Security\Permission;

class AuthorizatorFactory
{
    public static function create(): Permission
    {
        $acl = new Permission;

        // Role
        $acl->addRole('user');
        $acl->addRole('admin');

        // Zdroje
        $acl->addResource('post');
        $acl->addResource('comment');

        // Práva pro usera
        $acl->allow('user', 'post', ['view']);
        $acl->allow('user', 'comment', ['view', 'add']);

        // Admin může vše
        $acl->allow('admin', Permission::ALL, Permission::ALL);

        return $acl;
    }
}
