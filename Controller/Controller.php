<?php
namespace Controller;

use Lib\Request\Request;
use Lib\Response\AjaxResponse;
use Lib\Response\RedirectResponse;
use Lib\Response\Response;
use Lib\Response\ViewResponse;
use Lib\UserSession;
use View\MainLayoutView;
use View\UserbarView;

/**
 * Controller
 * 
 * Базовый контроллер
 *
 * @author i.spirin
 */
abstract class Controller
{

	public const PAGE_SIZE = 30;

	public function createPage(Request $request): MainLayoutView
	{
		$layout = new MainLayoutView([
			'controllerRoute' => $request->get('route'),
		]);
		if (UserSession::get()->isAuthorized()) {
			$layout->setNavBar(new UserbarView());
		}
		return $layout;
	}

	public function viewResponse(Request $request, \View\View $content): Response
	{
		return new ViewResponse($this->createPage($request)->setContent($content));
	}

	public function ajaxResponse($data): Response
	{
		return new AjaxResponse(true, $data);
	}

	public function ajaxErrorResponse(string $errorMessage, $data = null): Response
	{
		return new AjaxResponse(false, $data, $errorMessage);
	}

	public function redirectResponse(string $url): Response
	{
		return new RedirectResponse($url);
	}
}
