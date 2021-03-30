<?php
namespace Lib\Request;

/**
 * Простой Request
 *
 * @author zoon-dev
 */
class Request
{

	public function get(string $name, $default = null)
	{
		return array_key_exists($name, $_REQUEST) ? $_REQUEST[$name] : $default;
	}

	public function setRoute(string $route): void
	{
		$_REQUEST['route'] = $route;
	}

	public function getRoute(): string
	{
		return $this->get('route');
	}
}
