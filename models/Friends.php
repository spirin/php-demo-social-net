<?php

namespace DemoSocial;

/**
 * Friends
 * 
 * Друзья
 *
 * @author i.spirin
 */
class Friends extends Model
{

	public function getAttributes()
	{
		return array(
			'id' => '',
			'date' => '',
			'fromid' => '',
			'toid' => '',
		);
	}

	/**
	 * Добавляет в список друзей
	 * 
	 * @param type $from
	 * @param type $to
	 */
	public function addFriends($from, $to)
	{
		$this->insert(array(
			'fromid' => (int) $from,
			'toid' => (int) $to,
		));
		$this->insert(array(
			'fromid' => (int) $to,
			'toid' => (int) $from
		));
	}

	/**
	 * Удаляет из друзей
	 * 
	 * @param type $from
	 * @param type $to
	 */
	public function deleteFriends($from, $to)
	{
		$this->getConnection()->query('delete from demosocial.friends where fromid = ? and toid = ?', array((int) $from, (int) $to));
		$this->getConnection()->query('delete from demosocial.friends where fromid = ? and toid = ?', array((int) $to, (int) $from));
	}

	/**
	 * Проверяет наличие дружбы
	 * 
	 * @param type $from
	 * @param type $to
	 * @return type
	 */
	public function checkFriend($from, $to)
	{
		$count = (int) $this->getConnection()->query(
				'select count(id) from demosocial.friends where fromid = ? and toid = ?', array((int) $from, (int) $to)
			)->fetchOne();

		return $count > 0;
	}

	/**
	 * Возвращает список друзей
	 * 
	 * @param type $id
	 * @return type
	 */
	public function searchFriendsFor($id)
	{
		$query = $this->getConnection()->query(
			'select df.id, du.id as uid, du.firstname, du.lastname from'
			. ' demosocial.friends as df left join demosocial.users as du on df.toid = du.id  '
			. 'where fromid=?', array((int) $id)
		);

		return $query->fetchArray();
	}

	public function getTableName()
	{
		return 'friends';
	}

}
