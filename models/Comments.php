<?php

namespace DemoSocial;

/**
 * Comments
 *
 * @author i.spirin
 */
class Comments extends Model
{

	/**
	 * Личные сообщения
	 */
	const TARGET_USERPM = 'pm';
	/**
	 * Комментарии к сообщениям на стене
	 */
	const TARGET_WALL = 'wl';
	/**
	 * Сообщения на стене
	 */
	const TARGET_WALLPOST = 'wp';

	public function getAttributes()
	{
		return array(
			'id' => '',
			'date' => '',
			'userid' => '',
			'target' => '',
			'targetid' => '',
			'content' => '',
			'treeid' => '',
			'unread' => '',
		);
	}

	/**
	 * Добавляет комментарий
	 * 
	 * @param type $userid
	 * @param type $content
	 * @param type $targetType
	 * @param type $targetId
	 * @param type $treeId
	 * @return type
	 */
	public function add($userid, $content, $targetType, $targetId, $treeId)
	{
		return $this->insert(array(
				'userid' => $userid,
				'target' => $targetType,
				'targetid' => $targetId,
				'content' => $content,
				'treeid' => $treeId,
		));
	}

	/**
	 * Поиск комментариев
	 * 
	 * @param type $targetType
	 * @param type $targetId
	 * @param type $treeId
	 * @param type $offset
	 * @param type $limit
	 * @return type
	 */
	public function search($targetType, $targetId, $treeId, $offset, $limit)
	{
		$sql = 'select dc.id,dc.userid as uid,dc.date,dc.content,du.firstname,du.lastname from demosocial.comments as dc left join demosocial.users as du on dc.userid = du.id '
			. 'where target=? and targetid=? and treeid=? order by date desc limit ?,?';

		$query = $this->getConnection()->query($sql, array($targetType, (int) $targetId, (int) $treeId, (int) $offset, (int) $limit));

		return $query->fetchArray();
	}

	/**
	 * Поиск сообщений из переписки с другом
	 * 
	 * @param type $userId
	 * @param type $targetId
	 * @param type $offset
	 * @param type $limit
	 * @return type
	 */
	public function searchChat($userId, $targetId, $offset, $limit)
	{
		$sql = 'select * from demosocial.comments where ((userid=? and targetid=?) or (targetid=? and userid=?)) order by date desc limit ?,?';

		$query = $this->getConnection()->query($sql, array((int) $userId, (int) $targetId, (int) $userId, (int) $targetId, (int) $offset, (int) $limit));

		return $query->fetchArray();
	}

	/**
	 * Поиск переписок с друзьями
	 * 
	 * @param type $userId
	 * @return type
	 */
	public function searchChats($userId)
	{
		$sql = 'select dc.userid as uid,dc.date,du.firstname,du.lastname '
			. 'from demosocial.comments as dc left join demosocial.users as du on dc.userid = du.id '
			. 'where dc.targetid=? group by uid union ';
		$sql .= 'select dc.targetid as uid,dc.date,du.firstname,du.lastname '
			. 'from demosocial.comments as dc left join demosocial.users as du on dc.targetid = du.id '
			. 'where dc.userid=? group by uid';

		$rows = $this->getConnection()->query($sql, array((int) $userId, (int) $userId,))->fetchArray();

		$results = array();

		foreach ($rows as $row)
		{
			$row['uid'] = (int) $row['uid'];
			$results[$row['uid']] = $row;
		}

		return $results;
	}

	/**
	 * Количество непрочитанных сообщений
	 * 
	 * @param type $userId
	 * @return type
	 */
	public function searchChatUnreadCount($userId)
	{
		return (int) $this->getConnection()->query(
				'select count(id) from demosocial.comments where targetid=? and unread=1', array((int) $userId)
			)->fetchOne();
	}

	public function getTableName()
	{
		return 'comments';
	}

}
