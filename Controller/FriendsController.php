<?php
namespace Controller;

use Lib\Helper;
use Lib\Request\Request;
use Lib\Response\Response;
use Lib\UserSession;
use Source\FriendOffers;
use Source\Friends;
use Source\Users;
use View\BlockView;

/**
 * FriendsController
 * 
 * Друзья
 *
 * @author i.spirin
 */
class FriendsController extends Controller
{

	public function actionIndex(Request $request): Response
	{
		return $this->viewResponse($request, new BlockView('friends/index'));
	}

	public function actionSearch(Request $request): Response
	{
		return $this->viewResponse($request, new BlockView('friends/search'));
	}

	public function actionAjaxSearch(Request $request): Response
	{
		$query = Helper::safeText($request->get('query', ''));

		$results = Users::get()->searchOtherUsers($query, (int) $request->get('page') * self::PAGE_SIZE, self::PAGE_SIZE);

		return $this->ajaxResponse($results);
	}

	public function actionAjaxOffers(Request $request): Response
	{
		$groups = [
			'requested' => FriendOffers::STATUS_NEW,
			'confirmed' => FriendOffers::STATUS_CONFIRMED,
			'rejected' => FriendOffers::STATUS_REJECTED,
			'all' => '',
			'my' => '',
		];
		$group = (string) $request->get('group');
		if (!array_key_exists($group, $groups)) {
			return $this->ajaxErrorResponse('Unknown group');
		}

		$results = [];
		if ($group !== 'my' && $group !== 'all') {
			$results = FriendOffers::get()->searchOffersTo(UserSession::get()->id, $groups[$group]);
		} elseif ($group !== 'all') {
			$results = FriendOffers::get()->searchOffersFrom(UserSession::get()->id);
		} else {
			$results = Friends::get()->searchFriendsFor(UserSession::get()->id);
		}

		return $this->ajaxResponse($results);
	}

	public function actionAjaxRequest(Request $request): Response
	{
		$current = UserSession::get()->id;
		$id = (int) $request->get('id');

		if ($id && ($user = Users::get()->findById($id))) {
			if (FriendOffers::get()->checkNoOffer($current, $id)) {
				FriendOffers::get()->addOffer($current, $id);

				return $this->ajaxResponse('OK');
			}
			return $this->ajaxErrorResponse('Запрос уже существует');
		}
		return $this->ajaxErrorResponse('Пользователь не найден');
	}

	public function actionAjaxConfirm(Request $request): Response
	{
		$id = (int) $request->get('id');

		if ($id && ($offer = FriendOffers::get()->findById($id))) {
			FriendOffers::get()->confirmOffer($id);
			Friends::get()->addFriends(UserSession::get()->id, $offer['fromid']);

			return $this->ajaxResponse('OK');
		}
		return $this->ajaxErrorResponse('Запись не найдена');
	}

	public function actionAjaxReject(Request $request): Response
	{
		$id = (int) $request->get('id');

		if ($id && ($offer = FriendOffers::get()->findById($id))) {
			FriendOffers::get()->rejectOffer($id);
			Friends::get()->deleteFriends(UserSession::get()->id, $offer['fromid']);

			return $this->ajaxResponse('OK');
		}
		return $this->ajaxErrorResponse('Запись не найдена');
	}
}
