<?php
namespace Lib;

/**
 * Config
 *
 * @author i.spirin
 */
class Config
{

	/**
	 * Все значения
	 * 
	 * @var array
	 */
	protected static array $config;

	/**
	 * Вернет значение по указанному dot-notation пути
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get(string $name, $default = null)
	{
		return self::getByKeys(explode('.', $name), self::$config, $default);
	}

	/**
	 * Первоначальная загрузка
	 * 
	 * @param array $config
	 * @return void
	 */
	public static function load(array $config): void
	{
		self::$config = $config;
	}

	/**
	 * Выборка из общего конфига по ключам
	 * 
	 * @param array $keys
	 * @param mixed $data
	 * @param mixed $default
	 * @return mixed
	 */
	protected static function getByKeys(array $keys, $data, $default = null)
	{
		if (!is_array($data) || count($data) === 0) {
			return $default;
		}
		$key = array_shift($keys);
		if (!array_key_exists($key, $data)) {
			return $default;
		}
		return count($keys) > 0 ? self::getByKeys($keys, $data[$key], $default) : $data[$key];
	}
}
