<?php
namespace Lib;

use Lib\Request\Request;
use Lib\Response\RedirectResponse;
use Throwable;

/**
 * Router
 * 
 * @author i.spirin
 */
class Router extends Component
{

	/**
	 * @var array 
	 */
	public array $routes;

	protected function init(): void
	{
		$this->routes = Config::get('routes');
	}

	/**
	 * 
	 * @param string $route
	 * @return void
	 */
	public function handle(string $route = null): void
	{
		$request = new Request();
		$route = $route ?: preg_replace('/[^a-zA-Z0-9\-]/', '', substr($request->get('route', 'index'), 0, 255));

		if (!isset($this->routes[$route])) {
			$this->handle('error404');
			return;
		}
		$request->setRoute($route);

		if (!UserSession::get()->isAuthorized()) {
			if ($this->routes[$route]['guest'] === false) {
				(new RedirectResponse('/?route=login'))->output();
				return;
			}
		}

		$controllerName = $this->routes[$route]['controller'];
		$actionName = 'action' . ucfirst($this->routes[$route]['action']);

		try {
			call_user_func_array([new $controllerName, $actionName], [$request])->output();
		} catch (Throwable $ex) {
			$this->handle('error500');
		}
	}
}
