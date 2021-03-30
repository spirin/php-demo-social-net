<?php
namespace View;

use Lib\UserSession;

/**
 * ProfileView
 *
 * @author i.spirin
 */
class ProfileView extends View
{

	protected string $template = 'profile';

	public function __construct()
	{
		$this->data = ['sessionUser' => UserSession::get()->data];
	}
}
