<?php
namespace Source;

/**
 * FriendOffers
 * 
 * Заявки в друзья
 *
 * @author i.spirin
 */
class FriendOffers extends Source
{

	/**
	 * Новая заявка
	 */
	public const STATUS_NEW = 'nw';

	/**
	 * Заявка подверждена
	 */
	public const STATUS_CONFIRMED = 'cd';

	/**
	 * Заявка отклонена
	 */
	public const STATUS_REJECTED = 'rd';

	public function getAttributes(): array
	{
		return [
			'id' => '',
			'fromid' => '',
			'toid' => '',
			'status' => '',
		];
	}

	public function getTableName(): string
	{
		return 'friendOffers';
	}

	/**
	 * Ищет заявки в друзья по отправителю и получателю
	 * 
	 * @param int $from
	 * @param int $to
	 * @return array
	 */
	public function findByFromTo(int $from, int $to): ?array
	{
		return $this->getConnection()->query(
			'select * from demosocial.friendoffers where fromid = ? and toid = ?', [$from, $to]
		)->fetchRow();
	}

	/**
	 * Возвращает поступившие заявки в друзья
	 * 
	 * @param int $to
	 * @param string $status
	 * @return array
	 */
	public function searchOffersTo(int $to, string $status): array
	{
		$query = $this->getConnection()->query(
			'select df.id, df.status, du.id as uid, du.firstname, du.lastname '
			. 'from demosocial.friendoffers as df left join demosocial.users as du on df.fromid = du.id  '
			. 'where toid=? and status=?', [$to, $status]
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
	public function countOffersTo(int $to, string $status): int
	{
		return (int) $this->getConnection()->query(
			'select count(id) from demosocial.friendoffers where toid=? and status=?', [$to, $status]
		)->fetchOne();
	}

	/**
	 * Ищет завки созданные пользователем
	 * 
	 * @param type $from
	 * @return type
	 */
	public function searchOffersFrom(int $from)
	{
		$query = $this->getConnection()->query(
			'select df.id, df.status, du.id as uid, du.firstname, du.lastname from'
			. ' demosocial.friendoffers as df left join demosocial.users as du on df.toid = du.id  '
			. 'where fromid=?', [$from]
		);

		return $query->fetchArray();
	}

	/**
	 * Подтверждает заявку в друзья
	 * 
	 * @param type $id
	 */
	public function confirmOffer(int $id)
	{
		$this->updateById($id, ['status' => self::STATUS_CONFIRMED]);
	}

	/**
	 * Отклоняет заявку в друзья
	 * 
	 * @param int $id
	 * @return void
	 */
	public function rejectOffer(int $id): void
	{
		$this->updateById($id, ['status' => self::STATUS_REJECTED]);
	}

	/**
	 * Проверяет что заявок в друзья еще не было
	 * 
	 * @param int $from
	 * @param int $to
	 * @return bool
	 */
	public function checkNoOffer(int $from, int $to): bool
	{
		$count = (int) $this->getConnection()->query(
			'select count(id) from demosocial.friendoffers where fromid = ? and toid = ?', [$from, $to]
		)->fetchOne();

		return $count === 0;
	}

	/**
	 * Добавляет заявку в друзья
	 * 
	 * @param int $from
	 * @param int $to
	 * @return void
	 */
	public function addOffer(int $from, int $to): void
	{
		$this->insert([
			'fromid' => $from,
			'toid' => $to,
			'status' => self::STATUS_NEW,
		]);
	}
}
