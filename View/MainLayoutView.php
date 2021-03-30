<?php
namespace View;

use Lib\UserSession;

/**
 * MainLayoutView
 *
 * @author i.spirin
 */
class MainLayoutView extends View
{

	protected string $template = 'layouts/main';
	protected View $navBarView;
	protected View $contentView;

	public function __construct(array $data = [])
	{
		$this->data = $data;
		$user = UserSession::get();
		$this->data['userIsAuthorized'] = $user->isAuthorized();
		$this->data['sessionUser'] = $user->data;
	}

	public function setNavBar(View $view): MainLayoutView
	{
		$this->navBarView = $view;
		return $this;
	}

	public function setContent(View $view): MainLayoutView
	{
		$this->contentView = $view;
		return $this;
	}

	public function render(bool $toString = false): ?string
	{
		$this->data['navBar'] = $this->navBarView;
		$this->data['content'] = $this->contentView;
		return parent::render($toString);
	}
}
