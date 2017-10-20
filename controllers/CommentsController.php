<?php

namespace DemoSocial;

/**
 * CommentsController
 * 
 * Комментарии/посты
 *
 * @author i.spirin
 */
class CommentsController extends Controller
{

	public function actionIndex()
	{
		$this->render('comments/index', array(
			'chats' => array()
		));
	}

	public function actionAjaxComment()
	{
		$current = App::getInstance()->user->id;
		$target = (string) $_REQUEST['target'];
		$targetId = (int) $_REQUEST['targetId'];
		$treeId = (int) $_REQUEST['treeId'];
		$content = StringHelper::safeText($_REQUEST['content']);

		if (!$targetId)
		{
			return $this->sendJsonError('Не указана цель сообщения');
		}
		if ($target !== Comments::TARGET_USERPM && $target !== Comments::TARGET_WALL && $target !== Comments::TARGET_WALLPOST)
		{
			return $this->sendJsonError('Не верная цель сообщения');
		}
		if (!$content)
		{
			return $this->sendJsonError('Не указан текст сообщения');
		}

		$commentId = Comments::getInstance()->add($current, $content, $target, $targetId, $treeId);

		$this->sendJson(Comments::getInstance()->findById($commentId));
	}

	public function actionAjaxGetComments()
	{
		$current = App::getInstance()->user->id;
		$target = (string) $_REQUEST['target'];
		$targetId = (int) $_REQUEST['targetId'];
		$treeId = (int) $_REQUEST['treeId'];
		$limit = 30;
		$page = (int) @$_GET['page'];

		if (!$targetId)
		{
			return $this->sendJsonError('Не указана цель сообщения');
		}
		if ($target !== Comments::TARGET_USERPM && $target !== Comments::TARGET_WALL && $target !== Comments::TARGET_WALLPOST)
		{
			return $this->sendJsonError('Не верная цель сообщения');
		}

		$comments = Comments::getInstance()->search($target, $targetId, $treeId, $page * $limit, $limit);

		foreach (array_keys($comments) as $key)
		{
			$comments[$key]['content'] = htmlspecialchars($comments[$key]['content']);
		}

		$this->sendJson($comments);
	}

}
