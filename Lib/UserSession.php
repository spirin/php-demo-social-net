<?php
namespace Lib;

use Source\Users;

/**
 * Session
 * 
 * Сессия текущего пользователя
 *
 * @author i.spirin
 */
class UserSession extends Component
{

	/**
	 * Номер пользователя
	 *
	 * @var int|null 
	 */
	public ?int $id = null;

	/**
	 * Данные профиля пользователя
	 *
	 * @var array 
	 */
	public array $data = [];

	/**
	 * Запуск компонента с чтением активной сессии из кукис
	 * 
	 * @return void
	 * @throws \Exception
	 */
	protected function init(): void
	{
		$config = Config::get('session', []);
		$config['read_and_close'] = true;
		session_start($config);
		if (empty($_SESSION['id'])) {
			return;
		}
		if (($id = (int) $_SESSION['id']) && ($data = Users::get()->findById($id))) {
			$data['id'] = (int) $data['id'];
			unset($data['hash']);
			$this->id = $data['id'];
			$this->data = $data;
			return;
		}
		$this->logout();
		throw new \Exception('Invalid session id');
	}

	/**
	 * Проверяет авторизован ли текущий пользователь
	 * 
	 * @return bool
	 */
	public function isAuthorized(): bool
	{
		return !empty($this->id);
	}

	/**
	 * Авторизует пользователя
	 * 
	 * @param string $identity
	 * @param string $password
	 * @return bool
	 */
	public function login(string $identity, string $password): bool
	{
		$data = Users::get()->findByIdentity($identity, $password);
		if (!$data) {
			return false;
		}
		$data['id'] = (int) $data['id'];
		unset($data['hash']);
		$this->id = $data['id'];
		$this->data = $data;
		session_start(Config::get('session', []));
		$_SESSION['id'] = $data['id'];
		session_write_close();
		return true;
	}

	/**
	 * Прекращение активной сессии
	 * 
	 * @return void
	 */
	public function logout(): void
	{
		$this->id = null;
		$this->data = [];
		session_start(Config::get('session', []));
		unset($_SESSION['id']);
		session_write_close();
	}
}
