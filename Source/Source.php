<?php
namespace Source;

use Exception;
use Lib\MySQL;

/**
 * Abstract
 * 
 * Базовая источник данных в стиле DAL
 *
 * @author i.spirin
 */
abstract class Source
{

	/**
	 * @var Source[]
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
		}
		return self::$instances[$className];
	}

	/**
	 * @return string
	 */
	abstract public function getTableName(): string;

	/**
	 * Список полей для контроля при вставке и обновлении
	 * 
	 * @return array
	 */
	abstract public function getAttributes(): array;

	/**
	 * @return array
	 */
	public function findAll(): array
	{
		$query = $this->getConnection()->query('select * from `' . $this->getTableName() . '`');

		return $query->fetchArray();
	}

	/**
	 * @param int[] $ids
	 * @return array
	 */
	public function findAllByIds(array $ids): array
	{
		$query = $this->getConnection()->query('select * from `' . $this->getTableName() . '` where id=?', array_map('\intval', $ids));

		return $query->fetchArray();
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function findById(int $id): array
	{
		$query = $this->getConnection()->query('select * from `' . $this->getTableName() . '` where id=?', [$id]);

		return $query->fetchRow();
	}

	/**
	 * @param int $id
	 * @param array $data
	 * @return int
	 */
	public function updateById(int $id, array $data): int
	{
		return $this->update($data, 'id=?', [$id]);
	}

	/**
	 * @param array $data
	 * @param string $whereQuery
	 * @param array $params
	 * @return int
	 * @throws Exception
	 */
	public function update(array $data, string $whereQuery, array $params): int
	{
		unset($data['id']);
		$attributes = $this->getAttributes();

		$setSql = [];
		foreach (array_keys($data) as $name) {
			if (!isset($attributes[$name])) {
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
	 * @param array $data
	 * @return int
	 * @throws Exception
	 */
	public function insert(array $data): int
	{
		unset($data['id']);
		$attributes = $this->getAttributes();

		foreach (array_keys($data) as $name) {
			if (!isset($attributes[$name])) {
				throw new Exception('Unknown attribute [' . $name . '] for model [' . __CLASS__ . '] on insert');
			}
		}

		$sql = 'insert into `' . $this->getTableName() . '` (' . implode(',', array_keys($data)) . ') values (';
		$sql .= rtrim(str_repeat('?,', count($data)), ',') . ')';

		$this->getConnection()->query($sql, $data);
		return $this->getConnection()->getLastInsertId();
	}

	protected function getConnection(): MySQL
	{
		return MySQL::get();
	}
}
