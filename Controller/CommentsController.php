<?php
namespace Controller;

use Lib\Helper;
use Lib\Request\Request;
use Lib\Response\Response;
use Lib\UserSession;
use Source\Comments;
use View\BlockView;

/**
 * CommentsController
 * 
 * Комментарии/посты
 *
 * @author i.spirin
 */
class CommentsController extends Controller
{

	public function actionIndex(Request $request): Response
	{
		return $this->viewResponse($request, new BlockView('comments/index', ['chats' => []]));
	}

	public function actionAjaxComment(Request $request): Response
	{
		$current = UserSession::get()->id;
		$target = (string) $request->get('target');
		$targetId = (int) $request->get('targetId');
		$treeId = (int) $request->get('treeId');
		$content = Helper::safeText($request->get('content', ''));

		if (!$targetId) {
			return $this->ajaxErrorResponse('Не указана цель сообщения');
		}
		if (!in_array($target, Comments::VALID_TARGETS)) {
			return $this->ajaxErrorResponse('Не верная цель сообщения');
		}
		if (!$content) {
			return $this->ajaxErrorResponse('Не указан текст сообщения');
		}

		$commentId = Comments::get()->add($current, $content, $target, $targetId, $treeId);

		return $this->ajaxResponse(Comments::get()->findById($commentId));
	}

	public function actionAjaxGetComments(Request $request): Response
	{
		$target = (string) $request->get('target');
		$targetId = (int) $request->get('targetId');
		$treeId = (int) $request->get('treeId');

		if (!$targetId) {
			return $this->ajaxErrorResponse('Не указана цель сообщения');
		}
		if (!in_array($target, Comments::VALID_TARGETS)) {
			return $this->ajaxErrorResponse('Не верная цель сообщения');
		}

		$comments = Comments::get()->search($target, $targetId, $treeId, (int) $request->get('page') * self::PAGE_SIZE, self::PAGE_SIZE);
		foreach (array_keys($comments) as $key) {
			$comments[$key]['content'] = htmlspecialchars($comments[$key]['content']);
		}

		return $this->ajaxResponse($comments);
	}
}
