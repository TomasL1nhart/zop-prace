<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList;

        // Admin route
        $router->addRoute('admin', 'Admin:default');

        // Ostatní presentery
        $router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');

        return $router;
    }
}
