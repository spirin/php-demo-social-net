<?php
namespace Controller;

use Lib\Request\Request;
use Lib\Response\Response;
use Lib\UserSession;
use Source\FriendOffers;
use Source\Friends;
use Source\Users;
use View\BlockView;

/**
 * WallController
 *
 * Стена
 * 
 * @author i.spirin
 */
class WallController extends Controller
{

	public function actionIndex(Request $request): Response
	{
		$id = (int) $request->get('id');

		if (!$id || !($user = Users::get()->findById($id))) {
			return $this->redirectResponse('/?route=wall&id=' . UserSession::get()->id);
		}
		$showFriendOfferNew = false;
		$showFriendOfferWait = false;
		$showFriendOfferRejected = false;
		$userId = UserSession::get()->id;

		$showMessageNew = false;
		if ($userId !== $id) {
			if (!Friends::get()->checkFriend($userId, $id)) {
				if ($offer = FriendOffers::get()->findByFromTo($userId, $id)) {
					$offer['status'] = $offer['status'];

					// TODO перекрестный запрос в друзья
					if ($offer['status'] === FriendOffers::STATUS_NEW) {
						$showFriendOfferWait = true;
					} elseif ($offer['status'] === FriendOffers::STATUS_REJECTED) {
						$showFriendOfferRejected = true;
					}
				} else {
					$showFriendOfferNew = true;
				}
			}
		} else {
			$showMessageNew = true;
		}

		return $this->viewResponse($request, new BlockView('wall/index', [
			'wallUser' => $user,
			'sessionUser' => UserSession::get()->data,
			'showMessageNew' => $showMessageNew,
			'showFriendOfferNew' => $showFriendOfferNew,
			'showFriendOfferWait' => $showFriendOfferWait,
			'showFriendOfferRejected' => $showFriendOfferRejected,
		]));
	}
}
