<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
        $localeRoute = "[<locale=en>/]";

        $router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		$router->addRoute('<presenter>/<action>/[/<id>]', 'Employees:edit');
		$router->addRoute('<presenter>/<action>/[/<id>]', 'Employees:delete');
		return $router;
	}
}
