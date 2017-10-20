<?php

namespace DemoSocial;

/**
 * MySQL
 * 
 * Простой адаптер к БД
 *
 * @author i.spirin
 */
class MySQL extends Component
{

	/**
	 * Подключение к БД
	 *
	 * @var mysqli 
	 */
	protected $connection;

	/**
	 * Результат последнего запроса
	 *
	 * @var mysqli_result 
	 */
	protected $result;

	/**
	 * Загрузка компонента с подключением к БД
	 */
	public function init()
	{
		$this->connect(App::getInstance()->config['components']['mysql']);
	}

	/**
	 * Подключиться к БД
	 * 
	 * @param array $conf
	 * @return Metrika_MysqlAdapter
	 * 
	 * @throws Exception
	 */
	public function connect($conf)
	{
		if (!isset($conf['host'], $conf['name'], $conf['username'], $conf['password']))
		{
			throw new Exception('Invalid DB configuration');
		}

		for ($i = 0; $i < 5; $i++)
		{
			if ($this->connection = @mysqli_connect($conf['host'], $conf['username'], $conf['password']))
			{
				break;
			}
			usleep(250000); // 0.25s
		}
		if (!$this->connection)
		{
			throw new Exception('Can\'t connect to mysql: ' . mb_convert_encoding(mysqli_connect_error(), 'UTF8'));
		}

		if (!mysqli_select_db($this->connection, $conf['name']))
		{
			throw new Exception('Can\'t select db: ' . $this->getLastError());
		}

		mysqli_set_charset($this->connection, 'UTF8');

		return $this;
	}

	/**
	 * Отключается от БД
	 */
	public function disconnect()
	{
		@mysqli_close($this->connection);
	}

	/**
	 * Выполняет запрос к БД с фильтрацией запроса
	 * @param string $query
	 * @return MySQL
	 */
	public function query($query, $params = array())
	{
		$query = $this->compile($query, $params);

		//echo '<pre>', $query, '</pre>';

		$this->result = mysqli_query($this->connection, $query);

		if (mysqli_errno($this->connection))
		{
			throw new \Exception($this->getLastError());
		}

		return $this;
	}

	/**
	 * Возвращает аргумент в зависимости от типа с кавычками или без + фильтрация
	 * 
	 * @param mixed $param
	 * @return string
	 */
	public function compileParam($param)
	{
		if (is_null($param))
		{
			return 'null';
		}
		if (is_object($param) && method_exists($param, '__toString'))
		{
			$param = (string) $param;
		}
		if (is_string($param) || is_numeric($param) || is_null($param))
		{
			$param = $this->cleanParam($param);
			return (is_numeric($param) && !preg_match('/[a-zA-Z]/', $param) && !preg_match('/^(0|\+)[\d]+$/', $param)) ? $param : ('\'' . $param . '\'');
		}
		throw new Exception('Invalid DB parameters');
	}

	/**
	 * Формирует запрос из строки с ? и соответсвующим массивом параметров
	 * 
	 * @param string $query
	 * @param array $params
	 * @return string
	 */
	public function compile($query, & $params)
	{
		if ($params || count($params))
		{
			if (!is_array($params))
			{
				$params = array($params);
			}
			$counter = 1;
			$replacements = array();
			foreach ($params as $param)
			{
				if (is_null($param))
				{
					$param = 'null';
				}
				elseif (is_array($param))
				{
					foreach ($param as $subKey => $subParam)
					{
						$param[$subKey] = $this->compileParam($subParam);
					}
					$param = implode(',', $param);
				}
				else
				{
					$param = $this->compileParam($param);
				}
				$replacements['##PLH' . ($counter++) . '##'] = $param;
			}
			$counter = 1;

			$callback = function($matches) use (&$counter)
				{
				return '##PLH' . ($counter++) . '##';
				};

			$query = preg_replace_callback('/\?/', $callback, $query);

			if (($counter - 1) != count($replacements))
			{
				throw new \Exception('Invalid parameters count');
			}
			return strtr($query, $replacements);
		}
		return $query;
	}

	/**
	 * Количество затронутых последним запросом рядов
	 * 
	 * @return int
	 */
	public function getAffectedRows()
	{
		return mysqli_affected_rows($this->connection);
	}

	/**
	 * Последний вставленный ид
	 * 
	 * @return mixed
	 */
	public function getLastInsertId()
	{
		return mysqli_insert_id($this->connection);
	}

	/**
	 * Выбрать запись как массив из результата последнего запроса
	 * 
	 * @return array
	 */
	public function fetchRow()
	{
		return mysqli_fetch_assoc($this->result);
	}

	/**
	 * Получить результат как массив записей
	 * 
	 * @return array
	 */
	public function fetchArray()
	{
		$result = array();
		while ($row = $this->fetchRow())
		{
			$result[] = $row;
		}
		$this->freeResult();
		return $result;
	}

	/**
	 * Получить массив состоящий из значений определенной ячейки
	 * 
	 * @param string $column
	 * @return array
	 */
	public function fetchCol($column = null)
	{
		$result = array();
		while ($row = $this->fetchRow())
		{
			$result[] = $row ? ($column ? $row[$column] : reset($row)) : null;
		}
		return $result;
	}

	/**
	 * Получить одно значение
	 * 
	 * @param string $column
	 * @return mixed
	 */
	public function fetchOne($column = null)
	{
		$row = $this->fetchRow();
		return $row ? ($column ? $row[$column] : reset($row)) : null;
	}

	/**
	 * Последняя ошибка запроса
	 * 
	 * @return string
	 */
	public function getLastError()
	{
		return mb_convert_encoding(mysqli_error($this->connection), 'UTF8');
	}

	/**
	 * Освождает ресурсы результата. После этого fetch.. методы не смогут выдавать 
	 * результат пока не будет выполнен очередной запрос
	 * 
	 */
	public function freeResult()
	{
		if (is_resource($this->result))
		{
			return mysqli_free_result($this->result);
		}
	}

	/**
	 * Запись нескольких значений в БД за один запрос
	 *
	 * @param string $table название таблицы
	 * @param array $params массив с массивами отображающими данные в каждой строке
	 * @throws Exception
	 */
	public function multiInsert($table, $params)
	{
		if ($params && is_array($params))
		{
			$columns = reset($params);
			if (!is_array($columns))
			{
				throw new Exception('Params must be array of arrays');
			}
			ksort($columns);

			$sql = 'insert into `' . $table . '` (' . implode(', ', array_keys($columns)) . ') values ';

			$valuesSet = array();
			foreach ($params as $param)
			{
				if (count($param) != count($columns))
				{
					throw new Api_Core_Source_Exception('Count of columns not equals count of values');
				}
				ksort($param);
				$valuesSet[] = '(' . implode(',', array_map(array($this, 'compileParam'), $param)) . ')';
			}
			$sql .= implode(',', $valuesSet);
			$this->query($sql);
			$this->freeResult();
		}
	}

	/**
	 * Чистка параметра стандартными средствами mysql
	 * 
	 * @param string $param
	 * @return string
	 */
	protected function cleanParam($param)
	{
		return mysqli_real_escape_string($this->connection, $param);
	}

}
