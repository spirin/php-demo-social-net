<?php

namespace DemoSocial;

/**
 * FriendOffers
 * 
 * Заявки в друзья
 *
 * @author i.spirin
 */
class FriendOffers extends Model
{

	/**
	 * Новая заявка
	 */
	const STATUS_NEW = 'nw';

	/**
	 * Заявка подверждена
	 */
	const STATUS_CONFIRMED = 'cd';

	/**
	 * Заявка отклонена
	 */
	const STATUS_REJECTED = 'rd';

	public function getAttributes()
	{
		return array(
			'id' => '',
			'fromid' => '',
			'toid' => '',
			'status' => '',
		);
	}

	/**
	 * Ищет заявки в друзья по отправителю и получателю
	 * 
	 * @param type $from
	 * @param type $to
	 * @return type
	 */
	public function findByFromTo($from, $to)
	{
		return $this->getConnection()->query(
				'select * from demosocial.friendoffers where fromid = ? and toid = ?', array((int) $from, (int) $to)
			)->fetchRow();
	}

	/**
	 * Возвращает поступившие заявки в друзья
	 * 
	 * @param type $to
	 * @param type $status
	 * @return type
	 */
	public function searchOffersTo($to, $status)
	{
		$query = $this->getConnection()->query(
			'select df.id, df.status, du.id as uid, du.firstname, du.lastname from'
			. ' demosocial.friendoffers as df left join demosocial.users as du on df.fromid = du.id  '
			. 'where toid=? and status=?', array((int) $to, $status)
		);

		return $query->fetchArray();
	}

	/**
	 * Считает поступившие заявки в друзья
	 * 
	 * @param type $to
	 * @param type $status
	 * @return type
	 */
	public function countOffersTo($to, $status)
	{
		return (int) $this->getConnection()->query(
				'select count(id) from demosocial.friendoffers where toid=? and status=?', array((int) $to, $status)
			)->fetchOne();
	}

	/**
	 * Ищет завки созданные пользователем
	 * 
	 * @param type $from
	 * @return type
	 */
	public function searchOffersFrom($from)
	{
		$query = $this->getConnection()->query(
			'select df.id, df.status, du.id as uid, du.firstname, du.lastname from'
			. ' demosocial.friendoffers as df left join demosocial.users as du on df.toid = du.id  '
			. 'where fromid=?', array((int) $from)
		);

		return $query->fetchArray();
	}

	/**
	 * Подтверждает заявку в друзья
	 * 
	 * @param type $id
	 */
	public function confirmOffer($id)
	{
		$this->updateById((int) $id, array(
			'status' => self::STATUS_CONFIRMED
		));
	}

	/**
	 * Отклоняет заявку в друзья
	 * 
	 * @param type $id
	 */
	public function rejectOffer($id)
	{
		$this->updateById((int) $id, array(
			'status' => self::STATUS_REJECTED
		));
	}

	/**
	 * Проверяет что заявок в друзья еще не было
	 * 
	 * @param type $from
	 * @param type $to
	 * @return type
	 */
	public function checkNoOffer($from, $to)
	{
		$count = (int) $this->getConnection()->query(
				'select count(id) from demosocial.friendoffers where fromid = ? and toid = ?', array((int) $from, (int) $to)
			)->fetchOne();

		return $count == 0;
	}

	/**
	 * Добавляет заявку в друзья
	 * 
	 * @param type $from
	 * @param type $to
	 */
	public function addOffer($from, $to)
	{
		$this->insert(array(
			'fromid' => (int) $from,
			'toid' => (int) $to,
			'status' => self::STATUS_NEW,
		));
	}

	public function getTableName()
	{
		return 'friendOffers';
	}

}
