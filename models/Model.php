<?php

namespace DemoSocial;

/**
 * Abstract
 * 
 * Базовая модель в стиле DAL
 *
 * @author i.spirin
 */
abstract class Model
{

	/**
	 * Экземпляры моделей
	 *
	 * @var Model 
	 */
	protected static $instances;

	/**
	 * Возвращает экземпляр нужной модели
	 * 
	 * @return static
	 */
	public static function getInstance()
	{
		$className = get_called_class();

		if (isset(self::$instances[$className]))
		{
			return self::$instances[$className];
		}
		return (self::$instances[$className] = new $className());
	}

	/**
	 * Наименование таблицы
	 * 
	 * @return string
	 */
	abstract public function getTableName();

	/**
	 * Список полей для контроля при вставке и обновлении
	 * 
	 * @return array
	 */
	abstract public function getAttributes();

	/**
	 * Выборка всех записей
	 * 
	 * @return array
	 */
	public function findAll()
	{
		$query = $this->getConnection()->query('select * from `' . $this->getTableName() . '`');

		return $query->fetchArray();
	}

	/**
	 * Выборка всех записей по номерам
	 * 
	 * @return array
	 */
	public function findAllByIds($ids)
	{
		$query = $this->getConnection()->query('select * from `' . $this->getTableName() . '` where id=?', array((int) $id));

		return $query->fetchArray();
	}

	/**
	 * Выборка записи по номеру
	 * 
	 * @return array
	 */
	public function findById($id)
	{
		$query = $this->getConnection()->query('select * from `' . $this->getTableName() . '` where id=?', array((int) $id));

		return $query->fetchRow();
	}

	/**
	 * Обновление по номеру
	 * 
	 * @param int $id
	 * @param array $data
	 * @return int
	 */
	public function updateById($id, $data)
	{
		return $this->update($data, 'id=?', array((int) $id));
	}

	/**
	 * Обнволение по условию
	 * 
	 * @return int
	 */
	public function update($data, $whereQuery, $params)
	{
		unset($data['id']);
		$attributes = $this->getAttributes();

		$setSql = array();
		foreach ($data as $name => $value)
		{
			if (!isset($attributes[$name]))
			{
				throw new Exception('Unknown attribute [' . $name . '] for model [' . __CLASS__ . '] on update');
			}
			$setSql[] = $name . '=?';
		}

		$this->getConnection()->query(
			'update `' . $this->getTableName() . '` set ' . implode(', ', $setSql) . ' where ' . $whereQuery, array_merge($data, $params)
		);

		return $this->getConnection()->getAffectedRows();
	}

	/**
	 * Вставляет новую запись
	 * 
	 * @return int
	 */
	public function insert($data)
	{
		unset($data['id']);
		$attributes = $this->getAttributes();

		foreach ($data as $name => $value)
		{
			if (!isset($attributes[$name]))
			{
				throw new Exception('Unknown attribute [' . $name . '] for model [' . __CLASS__ . '] on insert');
			}
		}

		$sql = 'insert into `' . $this->getTableName() . '` (' . implode(',', array_keys($data)) . ') values (';
		$sql .= rtrim(str_repeat('?,', count($data)), ',') . ')';

		$this->getConnection()->query($sql, $data);
		return $this->getConnection()->getLastInsertId();
	}

	/**
	 * 
	 * @return MySQL
	 */
	protected function getConnection()
	{
		return App::getInstance()->mysql;
	}

}
