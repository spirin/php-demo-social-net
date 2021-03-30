<?php
namespace Source;

/**
 * Comments
 *
 * @author i.spirin
 */
class Comments extends Source
{

	/**
	 * Личные сообщения
	 */
	public const TARGET_USERPM = 'pm';

	/**
	 * Комментарии к сообщениям на стене
	 */
	public const TARGET_WALL = 'wl';

	/**
	 * Сообщения на стене
	 */
	public const TARGET_WALLPOST = 'wp';

	public const VALID_TARGETS = [
		self::TARGET_USERPM,
		self::TARGET_WALL,
		self::TARGET_WALLPOST,
	];
	
	public function getAttributes(): array
	{
		return [
			'id' => '',
			'date' => '',
			'userid' => '',
			'target' => '',
			'targetid' => '',
			'content' => '',
			'treeid' => '',
			'unread' => '',
		];
	}

	public function getTableName(): string
	{
		return 'comments';
	}

	/**
	 * Добавляет комментарий
	 * 
	 * @param int $userid
	 * @param string $content
	 * @param string $targetType
	 * @param int $targetId
	 * @param int $treeId
	 * @return int
	 */
	public function add(int $userid, string $content, string $targetType, int $targetId, int $treeId): int
	{
		return $this->insert([
			'userid' => $userid,
			'target' => $targetType,
			'targetid' => $targetId,
			'content' => $content,
			'treeid' => $treeId,
		]);
	}

	/**
	 * Поиск комментариев
	 * 
	 * @param string $targetType
	 * @param int $targetId
	 * @param int $treeId
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function search(string $targetType, int $targetId, int $treeId, int $offset, int $limit): array
	{
		$sql = 'select dc.id,dc.userid as uid,dc.date,dc.content,du.firstname,du.lastname '
			. 'from demosocial.comments as dc left join demosocial.users as du on dc.userid = du.id '
			. 'where target=? and targetid=? and treeid=? order by date desc limit ?,?';

		$query = $this->getConnection()->query($sql, [$targetType, $targetId, $treeId, $offset, $limit]);

		return $query->fetchArray();
	}

	/**
	 * Поиск сообщений из переписки с другом
	 * 
	 * @param int $userId
	 * @param int $targetId
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function searchChat(int $userId, int $targetId, int $offset, int $limit): array
	{
		$sql = 'select * from demosocial.comments where ((userid=? and targetid=?) or (targetid=? and userid=?)) order by date desc limit ?,?';

		$query = $this->getConnection()->query($sql, [$userId, $targetId, $userId, $targetId, $offset, $limit]);

		return $query->fetchArray();
	}

	/**
	 * Поиск переписок с друзьями
	 * 
	 * @param int $userId
	 * @return array
	 */
	public function searchChats(int $userId): array
	{
		$sql = 'select dc.userid as uid, dc.date, du.firstname, du.lastname '
			. 'from demosocial.comments as dc left join demosocial.users as du on dc.userid = du.id '
			. 'where dc.targetid=? group by uid union ';
		$sql .= 'select dc.targetid as uid, dc.date, du.firstname, du.lastname '
			. 'from demosocial.comments as dc left join demosocial.users as du on dc.targetid = du.id '
			. 'where dc.userid=? group by uid';

		$rows = $this->getConnection()->query($sql, [$userId, $userId])->fetchArray();
		$results = [];
		foreach ($rows as $row) {
			$row['uid'] = (int) $row['uid'];
			$results[$row['uid']] = $row;
		}

		return $results;
	}

	/**
	 * Количество непрочитанных сообщений
	 * 
	 * @param int $userId
	 * @return int
	 */
	public function searchChatUnreadCount(int $userId): int
	{
		$sql = 'select count(id) from demosocial.comments where targetid=? and unread=1';
		return (int) $this->getConnection()->query($sql, $userId)->fetchOne();
	}
}
