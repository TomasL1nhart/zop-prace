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
        $acl->addRole('producer');
        $acl->addRole('admin');

        // Zdroje (resources)
        $acl->addResource('post');
        $acl->addResource('comment');

        // Práva pro roli user
        $acl->allow('user', 'post', ['view']); // Uživatel může zobrazit příspěvky
        $acl->allow('user', 'comment', ['view', 'add']); // Uživatel může vidět a přidávat komentáře

        // Práva pro roli producer
        $acl->allow('producer', 'post', ['view', 'create', 'edit']); // Producer může vytvářet, editovat a zobrazit příspěvky
        $acl->allow('producer', 'comment', ['view', 'add']); // Producer může vidět a přidávat komentáře

        // Admin může vše
        $acl->allow('admin', Permission::ALL, Permission::ALL); // Admin má plný přístup ke všem zdrojům

        return $acl;
    }
}
