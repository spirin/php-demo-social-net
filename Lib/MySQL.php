<?php
namespace Lib;

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
	 * 
	 * @var array
	 */
	public static array $defaultConfig = [
		'host' => null,
		'name' => null,
		'username' => null,
		'password' => null,
		'retry' => 5,
		'retry_timeout' => 5,
	];
	
	/**
	 * 
	 * @return void
	 */
	protected function init(): void
	{
		$this->connect(Config::get('mysql'));
	}

	/**
	 * Подключиться к БД
	 * 
	 * @param array $conf
	 * @return Metrika_MysqlAdapter
	 * 
	 * @throws Exception
	 */
	public function connect($conf): MySQL
	{
		if (!isset($conf['host'], $conf['name'], $conf['username'], $conf['password'])) {
			throw new \Exception('Invalid DB configuration');
		}
		$conf = array_replace(self::$defaultConfig, $conf);
		for ($i = 0; $i < 5; $i++) {
			$this->connection = mysqli_connect($conf['host'], $conf['username'], $conf['password']);
			if ($this->connection) {
				break;
			}
			usleep($conf['retry_timeout']);
		}
		if (!$this->connection) {
			throw new \Exception('Can\'t connect to mysql: ' . mb_convert_encoding(mysqli_connect_error(), 'UTF8'));
		}
		if (!mysqli_select_db($this->connection, $conf['name'])) {
			throw new \Exception('Can\'t select db: ' . $this->getLastError());
		}

		mysqli_set_charset($this->connection, 'UTF8');

		return $this;
	}

	/**
	 * Отключается от БД
	 * 
	 * @return void
	 */
	public function disconnect(): void
	{
		mysqli_close($this->connection);
	}

	/**
	 * Выполняет запрос к БД с фильтрацией запроса
	 * 
	 * @param string $query
	 * @param mixed $params
	 * @return MySQL
	 */
	public function query(string $query, $params = []): MySQL
	{
		$query = $this->compile($query, is_array($params) ? $params : [$params]);

		$this->result = mysqli_query($this->connection, $query);
		if (mysqli_errno($this->connection)) {
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
	protected function compileParam($param): string
	{
		if (is_null($param)) {
			return 'null';
		}
		if (is_object($param) && method_exists($param, '__toString')) {
			$param = (string) $param;
		}
		if (is_string($param) || is_numeric($param) || is_null($param)) {
			$param = $this->cleanParam($param);
			return (is_numeric($param) && !preg_match('/[a-zA-Z]/', $param) && !preg_match('/^(0|\+)[\d]+$/', $param)) ? $param : ('\'' . $param . '\'');
		}
		throw new \Exception('Invalid DB parameters');
	}

	/**
	 * Формирует запрос из строки с ? и соответсвующим массивом параметров
	 * 
	 * @param string $query
	 * @param array $params
	 * @return string
	 */
	protected function compile(string $query, array $params): string
	{
		if (!$params) {
			return $query;
		}
		if (!is_array($params)) {
			$params = [$params];
		}
		$counter = 1;
		$replacements = [];
		foreach ($params as $param) {
			if (is_null($param)) {
				$param = 'null';
			} elseif (is_array($param)) {
				foreach ($param as $subKey => $subParam) {
					$param[$subKey] = $this->compileParam($subParam);
				}
				$param = implode(',', $param);
			} else {
				$param = $this->compileParam($param);
			}
			$replacements['##PLH' . ($counter++) . '##'] = $param;
		}
		$counter = 1;
		$callback = function ($matches) use (&$counter) {
			return '##PLH' . ($counter++) . '##';
		};

		$query = preg_replace_callback('/\?/', $callback, $query);

		if (($counter - 1) !== count($replacements)) {
			throw new \Exception('Invalid parameters count');
		}
		return strtr($query, $replacements);
	}

	/**
	 * Количество затронутых последним запросом рядов
	 * 
	 * @return int
	 */
	public function getAffectedRows(): int
	{
		return mysqli_affected_rows($this->connection);
	}

	/**
	 * Последний вставленный ид
	 * 
	 * @return int
	 */
	public function getLastInsertId(): int
	{
		return mysqli_insert_id($this->connection);
	}

	/**
	 * Выбрать запись как массив из результата последнего запроса
	 * 
	 * @return array|null
	 */
	public function fetchRow(): ?array
	{
		return mysqli_fetch_assoc($this->result);
	}

	/**
	 * Получить результат как массив записей
	 * 
	 * @return array
	 */
	public function fetchArray(): array
	{
		$result = [];
		while ($row = $this->fetchRow()) {
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
	public function fetchCol(string $column = null): array
	{
		$result = [];
		while ($row = $this->fetchRow()) {
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
	public function fetchOne(string $column = null)
	{
		$row = $this->fetchRow();
		return $row ? ($column ? $row[$column] : reset($row)) : null;
	}

	/**
	 * Последняя ошибка запроса
	 * 
	 * @return string
	 */
	public function getLastError(): string
	{
		return mb_convert_encoding(mysqli_error($this->connection), 'UTF8');
	}

	/**
	 * Освождает ресурсы результата. После этого fetch.. методы не смогут выдавать 
	 * результат пока не будет выполнен очередной запрос
	 * 
	 */
	public function freeResult(): void
	{
		if (is_resource($this->result)) {
			mysqli_free_result($this->result);
		}
	}

	/**
	 * Чистка параметра стандартными средствами mysql
	 * 
	 * @param string $param
	 * @return string
	 */
	protected function cleanParam(string $param): string
	{
		return mysqli_real_escape_string($this->connection, $param);
	}
}
