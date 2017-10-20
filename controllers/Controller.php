<?php

namespace DemoSocial;

/**
 * Controller
 * 
 * Базовый контроллер
 *
 * @author i.spirin
 */
abstract class Controller
{

	/**
	 *
	 * @var array 
	 */
	protected $globalData;

	/**
	 *
	 * @var array 
	 */
	protected $scripts = array();

	/**
	 *
	 * @var string 
	 */
	protected $layout = 'layouts/main';

	public function __construct($route = null)
	{
		$this->globalData['userIsAuthorized'] = App::getInstance()->user->isAuthorized();
		$this->globalData['sessionUser'] = App::getInstance()->user->data;

		$this->globalData['sessionNewFriends'] = 0;
		$this->globalData['sessionNewMessages'] = 0;

		if (App::getInstance()->user->isAuthorized())
		{
			$this->globalData['sessionNewFriends'] = FriendOffers::getInstance()->countOffersTo(App::getInstance()->user->id, FriendOffers::STATUS_NEW);
			$this->globalData['sessionNewMessages'] = 0;
		}
	}

	public function setRoute($route)
	{
		$this->globalData['controllerRoute'] = $route;
	}

	public function getRoute()
	{
		return $this->globalData['controllerRoute'];
	}

	public function renderBlock($view, $data = array(), $return = false)
	{
		$this->registerGlobalData($data);
		extract($data);

		if ($return)
		{
			ob_start();
		}
		require SERVER_ROOT . '/views/' . $view . '.php';

		if ($return)
		{
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	}

	public function render($view, $data = array())
	{
		$this->registerGlobalData($data);
		$data['content'] = $this->renderBlock($view, $data, true);
		extract($data);

		require SERVER_ROOT . '/views/' . $this->layout . '.php';
	}

	public function registerGlobalData(&$data)
	{
		foreach ($this->globalData as $name => $value)
		{
			if (!isset($data[$name]))
			{
				$data[$name] = $value;
			}
		}
	}

	public function redirect($url)
	{
		header('Location: ' . $url);
		exit;
	}

	public function scriptStart()
	{
		ob_start();
	}

	public function scriptEnd()
	{
		$scriptText = ob_get_contents();
		ob_end_clean();
		$this->scripts[] = $scriptText;
	}

	public function sendJson($data)
	{
		echo json_encode(array(
			'success' => true,
			'error' => '',
			'data' => $data,
		));
	}

	public function sendJsonError($error, $data = null)
	{
		echo json_encode(array(
			'success' => false,
			'error' => $error,
			'data' => $data,
		));
	}

}
