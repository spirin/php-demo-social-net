<?php
namespace View;

use Lib\UserSession;
use Source\FriendOffers;

/**
 * UserbarView
 *
 * @author i.spirin
 */
class UserbarView extends View
{

	protected string $template = 'userbar';

	public function __construct()
	{
		$user = UserSession::get();
		$this->data = [
			'user' => $user->data,
			'sessionNewFriends' => 0,
			'sessionNewMessages' => 0,
		];
		if ($user->isAuthorized()) {
			$this->data['sessionNewFriends'] = FriendOffers::get()->countOffersTo($user->id, FriendOffers::STATUS_NEW);
			$this->data['sessionNewMessages'] = 0; //TODO
		}
	}
}
