<?php

namespace DemoSocial;

/**
 * WallController
 *
 * Стена
 * 
 * @author i.spirin
 */
class WallController extends Controller
{

	public function actionIndex()
	{
		$id = (int) $_GET['id'];

		if ($id && ($user = Users::getInstance()->findById($id)))
		{
			$showFriendOfferNew = false;
			$showFriendOfferWait = false;
			$showFriendOfferRejected = false;

			$showMessageNew = false;
			if (App::getInstance()->user->id !== $id)
			{
				if (!Friends::getInstance()->checkFriend(App::getInstance()->user->id, $id))
				{
					if ($offer = FriendOffers::getInstance()->findByFromTo(App::getInstance()->user->id, $id))
					{
						$offer['status'] = $offer['status'];

						// TODO перекрестный запрос в друзья
						if ($offer['status'] === FriendOffers::STATUS_NEW)
						{
							$showFriendOfferWait = true;
						}
						elseif ($offer['status'] === FriendOffers::STATUS_REJECTED)
						{
							$showFriendOfferRejected = true;
						}
					}
					else
					{
						$showFriendOfferNew = true;
					}
				}
			}
			else
			{
				$showMessageNew = true;
			}

			unset($user['hash']);
			return $this->render('wall/index', array(
					'wallUser' => $user,
					'showMessageNew' => $showMessageNew,
					'showFriendOfferNew' => $showFriendOfferNew,
					'showFriendOfferWait' => $showFriendOfferWait,
					'showFriendOfferRejected' => $showFriendOfferRejected,
			));
		}
		$this->redirect('/?route=wall&id=' . App::getInstance()->user->id);
	}

}
