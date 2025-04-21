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

        // Definování hlavní routy

        $router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');

        // Definování routy pro kategorie
        $router->addRoute('game/category/<id>', 'Game:genre'); 
        $router->addRoute('home/category/<id>', 'Home:category');

        
        return $router;
    }
}
