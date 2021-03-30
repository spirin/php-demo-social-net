<?php
namespace Controller;

use DateTime;
use Lib\Helper;
use Lib\Request\Request;
use Lib\Response\Response;
use Lib\Response\ViewResponse;
use Lib\UserSession;
use Source\Users;
use View\BlockView;
use View\ProfileView;

/**
 * SiteController
 * 
 * Общий контроллер сайта
 *
 * @author i.spirin
 */
class SiteController extends Controller
{

	public function actionIndex(Request $request): Response
	{
		return $this->redirectResponse('/?route=wall&id=' . UserSession::get()->id);
	}

	public function actionProfile(Request $request): Response
	{
		if ($request->get('submit')) {
			$userData = $this->fillUserDataFromRequest($request);
			if ($password = $request->get('password')) {
				$userData['hash'] = md5(Helper::trim($password));
			}
			Users::get()->updateById(UserSession::get()->id, $userData);

			return $this->redirectResponse('/?route=profile');
		}
		return $this->viewResponse($request, new ProfileView());
	}

	public function actionRegister(Request $request): Response
	{
		if ($request->get('submit')) {
			$userData = array_merge([
				'email' => preg_replace('/[^\@a-zA-Zа-яА-Я0-9_\-\.\+]/', '', $request->get('email')),
				'hash' => md5($request->get('password')),
				'phone' => preg_replace('/[^0-9\+]/', '', $request->get('phone')),
			], $this->fillUserDataFromRequest($request));

			Users::get()->insert($userData);
			UserSession::get()->login($userData['email'], $request->get('password'));
			return $this->redirectResponse('/');
		}

		return $this->viewResponse($request, new BlockView('register'));
	}

	public function actionLogin(Request $request): Response
	{
		if ($request->get('submit')) {
			if (UserSession::get()->login(preg_replace('/[^\@a-zA-Zа-яА-Я0-9_\-\.\+]/', '', $request->get('identity')), $request->get('password'))) {
				return $this->redirectResponse('/');
			}
		}
		return $this->viewResponse($request, new BlockView('login'));
	}

	public function actionLogout(Request $request): Response
	{
		UserSession::get()->logout();

		return $this->redirectResponse('/');
	}

	public function actionError404(Request $request): Response
	{
		return new ViewResponse(new BlockView('layouts/error404'));
	}

	public function actionError500(Request $request): Response
	{
		return new ViewResponse(new BlockView('layouts/error500'));
	}

	protected function fillUserDataFromRequest(Request $request): array
	{
		return [
			'firstname' => Helper::safeText($request->get('firstname', '')),
			'lastname' => Helper::safeText($request->get('lastname', '')),
			'about' => Helper::safeText($request->get('about', '')),
			'borndate' => DateTime::createFromFormat('d.m.Y', $request->get('borndate'))->setTime(0, 0, 0)->format('Y-m-d H:i:s'),
			'sex' => (int) $request->get('sex')
		];
	}
}
