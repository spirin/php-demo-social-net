<?php

namespace DemoSocial;

/**
 * FriendsController
 * 
 * Друзья
 *
 * @author i.spirin
 */
class FriendsController extends Controller
{

	public function actionIndex()
	{
		$this->render('friends/index');
	}

	public function actionSearch()
	{
		$this->render('friends/search');
	}

	public function actionAjaxSearch()
	{
		$query = StringHelper::safeText(@$_GET['query']);

		$limit = 30;
		$page = (int) @$_GET['page'];

		$results = Users::getInstance()->searchOtherUsers($query, $page * $limit, $limit);

		$this->sendJson($results);
	}

	public function actionAjaxOffers()
	{
		$groups = array(
			'requested' => FriendOffers::STATUS_NEW,
			'confirmed' => FriendOffers::STATUS_CONFIRMED,
			'rejected' => FriendOffers::STATUS_REJECTED,
			'all' => '',
			'my' => '',
		);
		$group = @$_GET['group'];
		if (!isset($groups[$group]))
		{
			return $this->sendJsonError('Unknown group');
		}

		$limit = 30;
		$page = (int) @$_GET['page'];
		$results = array();

		if ($group !== 'my' && $group !== 'all')
		{
			$results = FriendOffers::getInstance()->searchOffersTo(App::getInstance()->user->id, $groups[$group]);
		}
		elseif ($group !== 'all')
		{
			$results = FriendOffers::getInstance()->searchOffersFrom(App::getInstance()->user->id);
		}
		else
		{
			$results = Friends::getInstance()->searchFriendsFor(App::getInstance()->user->id);
		}

		$this->sendJson($results);
	}

	public function actionAjaxRequest()
	{
		$current = App::getInstance()->user->id;
		$id = (int) $_GET['id'];

		if ($id && ($user = Users::getInstance()->findById($id)))
		{
			if (FriendOffers::getInstance()->checkNoOffer($current, $id))
			{
				FriendOffers::getInstance()->addOffer($current, $id);

				$this->sendJson('OK');
			}
			else
			{
				$this->sendJsonError('Запрос уже существует');
			}
		}
		else
		{
			$this->sendJsonError('Пользователь не найден');
		}
	}

	public function actionAjaxConfirm()
	{
		$id = (int) $_GET['id'];

		if ($id && ($offer = FriendOffers::getInstance()->findById($id)))
		{
			FriendOffers::getInstance()->confirmOffer($id);
			Friends::getInstance()->addFriends(App::getInstance()->user->id, $offer['fromid']);

			$this->sendJson('OK');
		}
		else
		{
			$this->sendJsonError('Запись не найдена');
		}
	}

	public function actionAjaxReject()
	{
		$id = (int) $_GET['id'];

		if ($id && ($offer = FriendOffers::getInstance()->findById($id)))
		{
			FriendOffers::getInstance()->rejectOffer($id);
			Friends::getInstance()->deleteFriends(App::getInstance()->user->id, $offer['fromid']);

			$this->sendJson('OK');
		}
		else
		{
			$this->sendJsonError('Запись не найдена');
		}
	}

}
