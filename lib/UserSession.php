<?php

namespace DemoSocial;

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
	 * @var int 
	 */
	public $id;

	/**
	 * Данные профиля пользователя
	 *
	 * @var array 
	 */
	public $data;

	/**
	 * Запуск компонента с чтением активной сессии из кукис
	 */
	public function init()
	{
		$config = App::getInstance()->config['components']['session'];
		$config['read_and_close'] = true;
		session_start($config);
		if (!empty($_SESSION['id']))
		{
			if (($id = (int) $_SESSION['id']) && ($data = Users::getInstance()->findById($id)))
			{
				$data['id'] = (int) $data['id'];
				unset($data['hash']);
				$this->id = $data['id'];
				$this->data = $data;
			}
			else
			{
				$this->logout();
				
				throw new \Exception('Invalid session id');
			}
		}
	}

	/**
	 * Проверяет авторизован ли текущий пользователь
	 * 
	 * @return bool
	 */
	public function isAuthorized()
	{
		return !empty($this->id);
	}

	/**
	 * Авторизует пользователя
	 * 
	 * @param type $identity
	 * @param type $password
	 * @return bool
	 */
	public function login($identity, $password)
	{
		if ($data = Users::getInstance()->findByIdentity($identity, $password))
		{
			$data['id'] = (int) $data['id'];
			unset($data['hash']);
			$this->id = $data['id'];
			$this->data = $data;
			session_start(App::getInstance()->config['components']['session']);
			$_SESSION['id'] = $data['id'];
			session_write_close();
			return true;
		}
		return false;
	}

	/**
	 * Прекращение активной сессии
	 */
	public function logout()
	{
		$this->id = null;
		$this->data = null;
		session_start(App::getInstance()->config['components']['session']);
		unset($_SESSION['id']);
		session_write_close();
	}

}
