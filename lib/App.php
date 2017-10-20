<?php

namespace DemoSocial;

/**
 * App
 * 
 * Базовый компонент для доступа к настройкам и глобальным компонентам
 *
 * @property UserSession $user
 * @property MySQL $mysql
 * 
 * @author i.spirin
 */
class App
{

	/**
	 * Общие настройки
	 *
	 * @var array 
	 */
	public $config;

	/**
	 * Компоненты
	 *
	 * @var Component[] 
	 */
	protected $components;

	/**
	 * Экземпляр пиложения
	 *
	 * @var App 
	 */
	protected static $instance;

	/**
	 * Возвращает экземпляр приложения
	 * 
	 * @return App
	 */
	public static function getInstance()
	{
		return self::$instance ? self::$instance : (self::$instance = new App());
	}

	/**
	 * Запуск приложения с настройками
	 * 
	 * @param type $config
	 */
	public function init($config)
	{
		$this->config = $config;
		$this->components = array();
	}

	/**
	 * Выполняет веб запрос
	 */
	public static function run()
	{
		$route = preg_replace('/[^a-zA-Z0-9\-]/', '', substr(@$_GET['route'], 0, 255));

		if (!$route)
		{
			$route = 'index';
		}

		if (!isset(self::getInstance()->config['routes'][$route]))
		{
			$route = 'error404';
		}

		if (!self::getInstance()->user->isAuthorized())
		{
			if (@self::getInstance()->config['routes'][$route]['guest'] === false)
			{
				header('Location:/?route=login');

				return;
			}
		}

		$controllerName = self::getInstance()->config['routes'][$route]['controller'];
		$actionName = 'action' . ucfirst(self::getInstance()->config['routes'][$route]['action']);

		$controller = new $controllerName();
		$controller->setRoute($route);

		return $controller->$actionName();
	}

	/**
	 * Ленивая загрузка компонентов по конфигу
	 * 
	 * @param type $name
	 * @return type
	 * @throws Exception
	 */
	public function __get($name)
	{
		if (!isset($this->components[$name]))
		{
			if (isset($this->config['components'][$name]))
			{
				$this->components[$name] = new $this->config['components'][$name]['class'];
				$this->components[$name]->init();
			}
			else
			{
				throw new Exception('Unknown component name');
			}
		}
		return $this->components[$name];
	}

}
