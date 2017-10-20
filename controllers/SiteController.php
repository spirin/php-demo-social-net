<?php

namespace DemoSocial;

/**
 * SiteController
 * 
 * Общий контроллер сайта
 *
 * @author i.spirin
 */
class SiteController extends Controller
{

	public function actionIndex()
	{
		$this->redirect('/?route=wall&id=' . App::getInstance()->user->id);
	}

	public function actionProfile()
	{
		if (isset($_POST['submit']))
		{
			$userData = array(
				'firstname' => StringHelper::safeText($_POST['firstname']),
				'lastname' => StringHelper::safeText($_POST['lastname']),
				'about' => StringHelper::safeText($_POST['about']),
				'borndate' => \DateTime::createFromFormat('d.m.Y', $_POST['borndate'])->setTime(0, 0, 0)->format('Y-m-d H:i:s'),
				'sex' => (int) $_POST['sex']
			);

			if (!empty($_POST['password']))
			{
				$userData['hash'] = md5(StringHelper::trim($_POST['password']));
			}

			Users::getInstance()->updateById(App::getInstance()->user->id, $userData);

			$this->redirect('/?route=profile');
		}
		$this->render('profile');
	}

	public function actionRegister()
	{
		if (isset($_POST['submit']))
		{
			$userData = array(
				'email' => preg_replace('/[^\@a-zA-Zа-яА-Я0-9_\-\.\+]/', '', $_POST['email']),
				'hash' => md5($_POST['password']),
				'phone' => preg_replace('/[^0-9\+]/', '', $_POST['phone']),
				'firstname' => StringHelper::safeText($_POST['firstname']),
				'lastname' => StringHelper::safeText($_POST['lastname']),
				'about' => StringHelper::safeText($_POST['about']),
				'borndate' => \DateTime::createFromFormat('d.m.Y', $_POST['borndate'])->setTime(0, 0, 0)->format('Y-m-d H:i:s'),
				'sex' => (int) $_POST['sex']
			);

			Users::getInstance()->insert($userData);

			App::getInstance()->user->login($userData['email'], $_POST['password']);

			$this->redirect('/');
		}

		$this->render('register');
	}

	public function actionLogin()
	{
		if (isset($_POST['submit']))
		{
			if (App::getInstance()->user->login(preg_replace('/[^\@a-zA-Zа-яА-Я0-9_\-\.\+]/', '', $_POST['identity']), $_POST['password']))
			{
				$this->redirect('/');
			}
		}
		$this->render('login');
	}

	public function actionLogout()
	{
		App::getInstance()->user->logout();

		$this->redirect('/');
	}

	public function actionError404()
	{
		$this->render('error404');
	}

}
