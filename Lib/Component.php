<?php
namespace Lib;

/**
 * Component
 *
 * @author i.spirin
 */
abstract class Component
{

	/**
	 * @var Component[] 
	 */
	protected static array $instances = [];

	/**
	 * @return static
	 */
	public static function get()
	{
		$className = get_called_class();
		if (!array_key_exists($className, self::$instances)) {
			self::$instances[$className] = new $className();
			self::$instances[$className]->init();
		}
		return self::$instances[$className];
	}

	abstract protected function init(): void;
}
